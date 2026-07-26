# Cơ chế và thiết kế index

## Bắt đầu từ query shape

Một index tốt trả lời query cụ thể: bảng nào là điểm bắt đầu, cột nào join/filter, kết quả cần thứ tự nào, trả bao nhiêu hàng và cột nào được đọc. Không thiết kế index chỉ từ danh sách cột hoặc độ selectivity.

Với query:

```sql
SELECT id, total_amount, created_at
FROM orders
WHERE user_id = ?
  AND status = ?
  AND created_at < ?
ORDER BY created_at DESC, id DESC
LIMIT 20;
```

Ứng viên cần đánh giá là `(user_id, status, created_at, id)`. Hai equality predicate tạo prefix seek, phần order/range quyết định khả năng quét tiếp và `id` làm tie-breaker. Đây là ứng viên để EXPLAIN và benchmark, không phải công thức áp dụng cho mọi query.

## Equality, order, range và optional predicate

- Cột equality thường đứng trước để thu hẹp vùng index liên tục.
- Thứ tự `ORDER BY` quan trọng khi muốn tránh sort và dừng sớm theo `LIMIT`.
- Điều kiện range thường giới hạn khả năng dùng các key part sau để định vị trực tiếp. Các cột sau vẫn có thể hỗ trợ Index Condition Pushdown, filtering, ordering trong một số plan hoặc covering.
- Selectivity chỉ là một yếu tố; join order, correlation, data skew, số hàng cần trả và khả năng phục vụ sort cũng quan trọng.
- Predicate optional tạo nhiều query shape. Một composite index lớn hiếm khi tối ưu cho mọi tổ hợp; xác định các shape thực sự nóng trước.
- `OR` có thể dẫn đến index merge hoặc plan khác. Đôi khi tách thành `UNION ALL` đúng semantics dễ tối ưu hơn, nhưng phải đo và xử lý duplicate.

## Leftmost prefix chính xác

Index `(a, b, c)` có thể phục vụ các prefix bắt đầu từ `a`. Bỏ `a` thường không thể seek theo `b, c` như một index độc lập, dù optimizer có thể chọn full index scan hoặc khả năng riêng của phiên bản MySQL.

Khi `a = ? AND b BETWEEN ? AND ? AND c = ?`, MySQL có thể seek bằng `a, b`; `c` thường không mở rộng seek range nhưng vẫn có thể được kiểm tra trong index và giúp covering. Không mô tả điều này là "`c` hoàn toàn vô dụng".

`LIKE 'abc%'` có thể dùng range; `LIKE '%abc'` thường không dùng B-Tree để seek theo prefix. Collation, implicit conversion và expression trên cột có thể thay đổi plan.

## Covering index và chi phí ghi

Secondary index InnoDB chứa secondary key và primary key. Nếu index có đủ dữ liệu query cần, optimizer có thể tránh lookup về clustered index. Lợi ích lớn nhất thường xuất hiện ở scan nhiều candidate rows nhưng trả ít cột.

Đổi lại, mỗi cột thêm vào index:

- tăng dung lượng và cache footprint;
- tăng I/O/CPU cho insert, update, delete;
- có thể giảm fan-out và tăng page split;
- kéo dài migration và bảo trì.

Không thêm cột payload lớn chỉ để đạt covering. So sánh latency đọc với write throughput và kích thước index.

## Đọc EXPLAIN theo ngữ cảnh

Kiểm tra đồng thời:

| Trường | Câu hỏi |
|---|---|
| `key`, `possible_keys` | Optimizer chọn index nào, vì sao candidate khác không được chọn? |
| `key_len` | Bao nhiêu key part thực sự tham gia truy cập? |
| `rows` | Ước lượng số hàng phải xem; có lệch lớn do statistics/skew không? |
| `filtered` | Bao nhiêu phần trăm còn lại sau filter? |
| `ref` và join order | Join được tra bằng key nào và bảng nào đi trước? |
| `Extra` | Có covering, ICP, sort, temporary table hay residual condition không? |

`ALL` có thể đúng với bảng nhỏ hoặc query đọc phần lớn bảng. `Using filesort` có thể rẻ hơn index scan cộng nhiều random lookup. `Using temporary` có thể là cách thực thi hợp lý cho aggregate. Cần so rows, bytes và actual timing thay vì đánh dấu lỗi chỉ từ nhãn.

Nếu production MySQL hỗ trợ, `EXPLAIN ANALYZE` cung cấp actual rows/loops/timing nhưng thực thi query; dùng thận trọng với query ghi hoặc workload nhạy cảm. Với `EXPLAIN` thường, nhớ rằng `rows` và `filtered` là estimate.

## Index trùng và dư thừa

- `(a, b)` thường dư thừa khi đã có `(a, b, c)`, nhưng chỉ xóa sau khi kiểm tra uniqueness, prefix length, sort direction, covering và query khác.
- `(a)` không thay thế `(b, a)`.
- Unique index vừa enforce invariant vừa là access path; không xóa chỉ vì một non-unique index có cùng prefix.
- FK index do MySQL yêu cầu có thể không đủ cho query dùng thêm status/order.

Trước khi thêm index: inventory index hiện tại, tìm overlap, đo write cost. Trước khi xóa: kiểm tra workload/slow log, deploy quan sát được và có rollback.

## Partitioning và full-text là công cụ nâng cao

### Partitioning

Chỉ cân nhắc khi partition pruning, lifecycle/retention hoặc vận hành bảng lớn mang lại lợi ích rõ. Partition key ảnh hưởng unique key, query shape và maintenance. Query không prune có thể chạm nhiều partition. Cần benchmark trên phân bố dữ liệu và số partition dự kiến; không chọn theo ngưỡng hàng cố định.

### Full-text search

MySQL FULLTEXT phù hợp khi semantics tokenization, language, ranking và vận hành đáp ứng yêu cầu. Search engine ngoài phù hợp khi cần analyzer tùy biến, typo tolerance, relevance phức tạp, distributed search hoặc scale/availability độc lập. Quyết định dựa trên tính năng, SLO và chi phí vận hành, không dựa trên số hàng tùy tiện.

Optimizer hints như `FORCE INDEX` hoặc join-order hint chỉ nên dùng sau khi statistics, query shape và index đã được kiểm tra. Hint tạo coupling với dữ liệu và phiên bản; luôn kèm benchmark, lý do và kế hoạch gỡ bỏ.

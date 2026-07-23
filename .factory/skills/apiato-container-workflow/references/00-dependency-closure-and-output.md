# Dependency Closure và Output Contract

## 1. Biến yêu cầu A thành A'

```txt
A = feature user gọi tên

A' =
  A
  + dependency bắt buộc để compile
  + dependency bắt buộc để đúng nghiệp vụ
  + integration contract
  + runtime cần để feature thật sự chạy
  + tests chứng minh toàn vẹn
```

Không tự mở rộng sang nghiệp vụ không phục vụ trực tiếp cho A.

## 2. Phân loại dependency

| Loại | Xử lý |
| --- | --- |
| Compile dependency | Tạo trước khi class khác import hoặc type-hint |
| Schema dependency | Chốt Model/table trước Migration/FK/validation |
| Business dependency | Phải có đủ field/invariant feature sử dụng, không tạo model rỗng |
| Cross-container contract | Chốt owner, IDs, permission, relation và response trước integration |
| Runtime dependency | Queue worker, scheduler, storage, provider, config/env phải có phase riêng |
| Future dependency | Không code sẵn; ghi extension point nếu chưa cần cho A' |

## 3. Thuật toán đóng scope

1. Đọc trạng thái thật của repo.
2. Liệt kê outputs mà feature hứa với API/UI/business.
3. Với mỗi output, truy ngược dữ liệu và hành vi cần có.
4. Với mỗi class/table/config chưa tồn tại, thêm node dependency.
5. Với mỗi side effect, thêm runtime node.
6. Với mỗi invariant, thêm test node.
7. Loại node không phục vụ output hoặc invariant.
8. Sắp xếp graph theo topological order.
9. Trình bày rõ `A -> A'` trước workflow.

## 4. Quy tắc khi dependency chưa tồn tại

- Không khai báo relation tới class chưa tồn tại.
- Không viết Transformer fallback phụ thuộc Customer nếu Customer chưa có `name`.
- Không tạo Customer chỉ có `id` nếu avatar fallback cần `name`.
- Không tạo Product chỉ có `id` nếu feature cần ownership, status hoặc SKU.
- Nếu business contract dependency chưa rõ và có nhiều hướng ảnh hưởng API/data, hỏi user trước.
- Nếu chỉ có một contract tối thiểu an toàn, đưa nó vào A' và ghi assumption.

## 5. Canonical Media closure

Yêu cầu:

```txt
Media cho Product tối đa 9 ảnh,
Customer tối đa 1 ảnh,
Customer không có ảnh thì tạo avatar từ tên.
```

Closure đúng:

```txt
Product contract tối thiểu phục vụ media
Customer contract tối thiểu có name phục vụ avatar fallback
Media persistence và storage
Product + Customer + Media migrations/models hoạt động độc lập
Cross-container morph contract
Upload/replace/reorder/delete
Fallback resolver và Customer response integration
Cleanup event/listener/job nếu cần
Queue worker/runtime nếu xử lý thumbnail/cleanup async
Cross-container tests
```

Thứ tự:

```txt
Chốt Product/Customer/Media contract
-> tạo và migrate Product + Customer foundations
-> tạo và migrate Media foundation
-> nối relation/morph map
-> viết Media use cases
-> tích hợp Customer avatar response
-> side effects/runtime
-> cross-container tests
```

## 6. Output bắt buộc

Workflow trả cho user phải có:

1. `Scope điều chỉnh`: A và A'.
2. `Assumptions`: chỉ các assumption chưa có evidence.
3. `Dependency order`: một dòng graph ngắn.
4. Các bước đã lọc.
5. Prompt copy-paste dưới từng bước.
6. Definition of Done theo invariant.
7. Không chèn code triển khai khi user chỉ xin quy trình.

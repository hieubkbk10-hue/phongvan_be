# Phân loại Subdomain

## Định nghĩa theo problem space

Phân loại áp dụng cho **Subdomain**, tức business capability hoặc cluster use case trong problem space. Phân loại luôn phụ thuộc tổ chức, thị trường và thời điểm; cùng một capability có thể mang nhãn khác ở doanh nghiệp khác hoặc đổi nhãn khi chiến lược thay đổi.

| Loại | Định nghĩa | Bằng chứng tích cực | Câu hỏi phản chứng |
|---|---|---|---|
| **Core** | Tạo competitive differentiation/advantage; khách hàng chọn hoặc trả tiền vì năng lực; chứa tri thức đặc thù và cần cải tiến liên tục | Customer-choice evidence, pricing/win-loss insight, proprietary policy/knowledge, learning loop, strategic investment | Nếu dùng giải pháp ngang thị trường, lý do khách hàng chọn doanh nghiệp có còn không? |
| **Supporting** | Cần để Core hoặc vận hành thành công; có custom behavior nhưng không phải lý do khách hàng chọn doanh nghiệp | Dependency vào Core, policy nội bộ đặc thù, giải pháp chuẩn không khớp constraints, vận hành cần ownership riêng | Custom behavior có thật sự tạo khác biệt hay chỉ phản ánh di sản? |
| **Generic** | Bài toán phổ biến, standardized, có sản phẩm/SaaS/thư viện hoặc thực hành trưởng thành trên thị trường | Vendor/category maturity, interchangeable capability, standard regulation/protocol, switching khả thi | Constraints nào khiến giải pháp phổ biến không còn tương đương? |
| **Unresolved** | Chưa đủ evidence để kết luận | Danh sách evidence thiếu và đúng authority cần hỏi | Quyết định nào sẽ thay đổi phân loại? |

Generic có thể critical, phức tạp và cần vận hành xuất sắc. Supporting có thể rất lớn. Core có thể đơn giản về mặt kỹ thuật. Nhãn không phải thước đo tầm quan trọng vận hành.

## Evidence matrix

| Khía cạnh | Core | Supporting | Generic |
|---|---|---|---|
| Customer choice | Ảnh hưởng trực tiếp, có evidence | Không trực tiếp | Không trực tiếp hoặc category expectation |
| Differentiation | Đặc thù và khó thay thế | Custom để hỗ trợ/vận hành | Chuẩn hóa hoặc dễ mua |
| Knowledge | Học hỏi nghiệp vụ liên tục tạo lợi thế | Tri thức nội bộ cần thiết | Tri thức phổ biến, vendor sở hữu phần lớn |
| Change cadence | Tiến hóa theo chiến lược/thị trường | Theo nhu cầu Core/vận hành | Theo vendor/standard/compliance |
| Market availability | Không có lựa chọn tương đương trong constraints | Có lựa chọn nhưng cần custom đáng kể | Có lựa chọn trưởng thành và phù hợp |
| Investment implication | Ưu tiên học hỏi và capability ownership | Đầu tư đủ để phục vụ Core | Ưu tiên leverage giải pháp có sẵn nếu phù hợp |

Không dùng một hàng đơn lẻ để quyết định. Ghi cả evidence ủng hộ, counterexample và alternative classification.

## Các non-signal bắt buộc loại bỏ

Những yếu tố sau không định nghĩa Core/Supporting/Generic:

- Độ phức tạp thuật toán hoặc tactical model.
- Traffic, throughput hoặc số người dùng.
- Criticality, uptime hoặc blast radius.
- Số bảng, entity, endpoint hoặc dòng code.
- Team hiện tại, kiến trúc hiện tại hoặc tên Apiato Container.
- Microservice, database riêng, Kafka, CQRS hoặc Event Sourcing.
- Chi phí đã đầu tư hoặc độ khó thay thế do legacy.

Các yếu tố này có thể ảnh hưởng quyết định kỹ thuật/downstream, nhưng không thay thế customer-value và market evidence.

## Classification Decision Record

```yaml
subdomain_classification:
  subdomain: "<tên theo Ubiquitous Language>"
  organization: "<tổ chức/đơn vị chiến lược>"
  market: "<phân khúc và phạm vi địa lý nếu có authority>"
  time_horizon: "<mốc thời gian hoặc giai đoạn chiến lược>"
  classification: "core | supporting | generic | unresolved"
  certainty: "Observation | Inference | Proposal | AcceptedAssumption | Decision | Blocker"
  confidence: "low | medium | high"
  authority:
    claim: "<claim được xác nhận>"
    owner: "<đúng authority>"
    source: "<phỏng vấn/tài liệu/dữ liệu>"
  evidence:
    customer_choice:
      - "<evidence hoặc missing>"
    differentiation:
      - "<evidence hoặc missing>"
    custom_behavior:
      - "<evidence hoặc not_applicable>"
    market_alternatives:
      - "<evidence hoặc missing>"
  counterexample:
    - "<dữ kiện có thể bác bỏ phân loại>"
  alternatives:
    - classification: "<nhãn khác>"
      condition: "<điều kiện khiến nhãn này đúng>"
  blockers:
    - "<evidence hoặc authority còn thiếu>"
  implications:
    investment: "<Proposal, không phải quyết định>"
    build_buy_partner: "<Proposal, không phải quyết định>"
  evolution:
    reclassification_triggers:
      - "<trigger có thể quan sát>"
    review_date: "<YYYY-MM-DD hoặc sự kiện review cụ thể>"
```

Không tạo điểm số phần trăm giả. `confidence` chỉ dùng `low`, `medium`, `high` và phải đi kèm evidence.

`AcceptedAssumption` không được thay customer-choice, differentiation hoặc market evidence. Nếu evidence bắt buộc còn thiếu, trường `classification` phải là `unresolved`; giả thuyết Core/Supporting/Generic chỉ được ghi trong `alternatives`, và Strategic Readiness Gate vẫn `BLOCKED` ở `classification_evidence`.

## Build, buy, partner chỉ là Proposal

Phân loại gợi ý hướng thẩm định, không tự động quyết định:

| Phân loại | Proposal thường cần đánh giá |
|---|---|
| Core | Build, co-create hoặc partner có bảo vệ learning loop và strategic control |
| Supporting | Build vừa đủ, outsource có guardrails hoặc mua rồi custom |
| Generic | Buy/SaaS/library trước, nhưng kiểm tra constraints, lock-in, compliance và integration |

Một Core Subdomain vẫn có thể dùng vendor cho commodity parts. Một Generic Subdomain vẫn có thể phải build khi constraints có evidence. Quyết định cuối cần cost, risk, legal, security và organizational authority phù hợp.

## Evolution và tái phân loại

### Supporting → Core

Trigger: customer behavior cho thấy capability trở thành lý do chọn/trả tiền; doanh nghiệp tạo learning loop hoặc IP riêng; chiến lược chuyển trọng tâm.

### Core → Generic

Trigger: thị trường chuẩn hóa; vendor đạt parity; khác biệt dịch chuyển sang capability khác; cost duy trì không còn tạo lợi thế.

### Generic → Core

Trigger: constraints mới hoặc cách kết hợp capability tạo khác biệt có bằng chứng; doanh nghiệp biến category expectation thành trải nghiệm khó sao chép.

### Các chuyển dịch khác

Core có thể thành Supporting khi vẫn cần custom nhưng không còn tạo customer choice. Supporting có thể thành Generic khi SaaS hoặc standard trưởng thành.

Mỗi record phải có review date hoặc review event. Review khi thay đổi chiến lược, phân khúc, regulation, vendor landscape, ownership, major incident hoặc discovery sâu làm lộ policy khác với strategic map.

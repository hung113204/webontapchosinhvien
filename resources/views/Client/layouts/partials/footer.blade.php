<footer>
  <div class="footer-top">
    <div class="container">
      <div class="footer-top-inner">
        <img src="{{ asset('frontend/asset/images/t2.png') }}" alt="Mascot IT Study" class="footer-mascot" />

        <div class="footer-intro">
          <h3>
            Hỗ trợ ôn tập
            <span class="accent2">Khoa Công nghệ Thông tin</span>
            là nơi sinh viên CNTT ôn luyện hiệu quả nhất.
          </h3>
          <p>
            Tại đây, sinh viên không chỉ ôn lại lý thuyết và bài tập — mà
            còn được luyện tư duy lập trình, thực hành qua đề thi thử và
            theo dõi tiến độ học tập theo từng học phần. Mỗi bài tập là một
            bước tiến trên con đường chinh phục kỳ thi và sự nghiệp số.
          </p>
        </div>

        <div class="footer-contact-info">
          <div class="ci-row">
            <span class="ci-label">Điện thoại:</span>
            <a href="tel:02812345678">09 9999 9999</a>
          </div>
          <div class="ci-row">
            <span class="ci-label">Zalo:</span>
            <a href="#">Hỗ trợ qua Zalo</a>
          </div>
          <div class="ci-row">
            <span class="ci-label">Email:</span>
            <a href="mailto:cntt@vhu.edu.vn">cntt@vhu.edu.vn</a>
          </div>
          <div class="ci-row">
            <span class="ci-label">Địa chỉ:</span>
            <span>Khoa CNTT, Trường Đại học , Hà Nội.</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="footer-mid">
    <div class="container">
      <div class="footer-mid-inner">
        <div class="footer-nav-col">
          <h4>Về chúng tôi</h4>
          <ul>
            <li><a href="#">Giới thiệu</a></li>
            <li><a href="#">Chính sách quyền riêng tư</a></li>
            <li><a href="#">Điều khoản sử dụng</a></li>
            <li><a href="#">Liên hệ / góp ý</a></li>
          </ul>
        </div>

        <div class="footer-nav-col">
          <h4>Khám phá</h4>
          <ul>
            <li><a href="#">Học phần</a></li>
            <li><a href="#">Lý thuyết</a></li>
            <li><a href="#">Luyện tập</a></li>
            <li><a href="#">Thi thử</a></li>
          </ul>
        </div>

        <div class="footer-nav-col">
          <h4>Tính năng</h4>
          <ul>
            <li><a href="#">Hỏi đáp AI</a></li>
            <li><a href="#">Theo dõi tiến độ</a></li>
            <li><a href="#">Bảng xếp hạng</a></li>
            <li><a href="#">Tài liệu ôn tập</a></li>
          </ul>
        </div>

        <div class="footer-qr">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=https://cntt-study.vhu.edu.vn" alt="QR Code truy cập website" />
          <div class="qr-label">Hỗ trợ ôn tập <span>Khoa CNTT</span></div>
          <div class="qr-sub">Quét QR để truy cập website</div>
        </div>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="container">
      Chủ quản: <strong>Khoa Công nghệ Thông tin – Trường Đại học </strong>.<br />
      Website hỗ trợ ôn tập học phần dành riêng cho sinh viên CNTT.<br />
      Chịu trách nhiệm nội dung: Bộ môn CNTT &nbsp;|&nbsp; Điện thoại: 099999999<br />
      Địa chỉ: Số 778 Điện Biên Phủ, P.25, Q. Bình Thạnh, Hà Nội.<br />
      © {{ date('Y') }} IT Study Support – Khoa CNTT, Trường Đại học . Bảo lưu mọi quyền.
    </div>
  </div>
</footer>

<script src="{{ asset('frontend/asset/js/nav.js') }}"></script>
@stack('scripts')
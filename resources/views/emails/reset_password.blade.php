<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Mã xác nhận OTP - IT Study Support</title>
</head>
<body style="margin:0; padding:0; background:#f0f7ff; font-family: 'Segoe UI', Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f7ff; padding: 40px 16px;">
        <tr>
            <td align="center">
                <table width="520" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:16px; overflow:hidden;
                           box-shadow: 0 4px 24px rgba(37,99,235,0.10); max-width:100%;">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #0f2a4a 0%, #163860 100%);
                                   padding: 32px 40px 28px; text-align: center;">
                            <img src="{{ $message->embed(public_path('frontend/asset/images/t2.png')) }}" alt="AI Logo" style="width: 70px; height: auto; margin-bottom: 12px; display: inline-block;" />
                            <div style="color:#ffffff; font-size:1.1rem; font-weight:800;">
                                IT Study Support
                            </div>
                            <div style="color:rgba(255,255,255,0.5); font-size:0.72rem; margin-top:2px;">
                                Khoa Công nghệ Thông tin – Học Viện Nông Nghiệp Việt Nam
                            </div>
                        </td>
                    </tr>

                    {{-- Nội dung chính --}}
                    <tr>
                        <td style="padding: 36px 40px; text-align:center;">
                            <h2 style="margin:0 0 16px; color:#0f2a4a; font-size:1.3rem; font-weight:800;">
                                Mã xác nhận khôi phục mật khẩu
                            </h2>
                            <p style="margin:0 0 24px; color:#64748b; font-size:0.88rem; line-height:1.6;">
                                Bạn đang thực hiện khôi phục mật khẩu cho tài khoản sinh viên.<br/>
                                Vui lòng sử dụng mã dưới đây để tiếp tục:
                            </p>

                            {{-- Khối hiển thị OTP --}}
                            <div style="background:#f8fafc; border:1.5px dashed #cbd5e1;
                                        border-radius:16px; padding:28px 24px;
                                        text-align:center; margin-bottom:24px;">
                                <div style="font-size:11px; font-weight:700; color:#94a3b8;
                                            letter-spacing:2px; text-transform:uppercase; margin-bottom:10px;">
                                    MÃ OTP CỦA BẠN
                                </div>
                                <div style="font-size:40px; font-weight:800; color:#2563eb;
                                            letter-spacing:12px; font-family: monospace;">
                                    {{ $otp }}
                                </div>
                            </div>

                            {{-- Thời gian hiệu lực --}}
                            <p style="margin:0 0 20px; color:#475569; font-size:0.85rem;">
                                Mã này có hiệu lực trong <strong style="color:#dc2626;">15 phút</strong>.
                                Không chia sẻ mã này với bất kỳ ai.
                            </p>

                            {{-- Cảnh báo nếu không phải bạn yêu cầu --}}
                            <div style="background:#fff7ed; border:1px solid #fdba74;
                                        border-radius:10px; padding:14px 18px; text-align:left;">
                                <p style="margin:0; font-size:0.78rem; color:#92400e; line-height:1.6;">
                                    <strong>⚠️ Không phải bạn yêu cầu?</strong><br/>
                                    Hãy bỏ qua email này. Mật khẩu của bạn sẽ không thay đổi
                                    nếu bạn không nhập mã này.
                                </p>
                            </div>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f8fafc; border-top:1.5px solid #e2eaf6;
                                   padding:20px; text-align:center;">
                            <p style="margin:0; font-size:0.72rem; color:#b0bec5;">
                                Đây là thư hệ thống, vui lòng không trả lời email này.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
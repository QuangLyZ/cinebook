<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phản hồi từ CineBook</title>
</head>
<body style="margin: 0; padding: 0; background-color: #030712; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #030712;">
        <tr>
            <td align="center" style="padding: 40px 0 60px 0;">
                <!-- Main Container -->
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #111827; border-radius: 24px; border: 1px solid #1f2937; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
                    
                    <!-- Top Gradient Accent -->
                    <tr>
                        <td height="4" style="background: linear-gradient(to right, #dc2626, #991b1b);"></td>
                    </tr>

                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 40px 40px 30px 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <div style="background-color: #dc2626; width: 60px; height: 60px; border-radius: 16px; margin-bottom: 20px; box-shadow: 0 10px 15px -3px rgba(220, 38, 38, 0.3);">
                                            <img src="https://img.icons8.com/ios-filled/100/ffffff/film-reel.png" width="30" height="30" style="padding: 15px; display: block;" alt="CineBook Logo">
                                        </div>
                                        <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 800; letter-spacing: -0.025em; text-transform: uppercase;">
                                            Cine<span style="color: #dc2626;">Book</span>
                                        </h1>
                                        <div style="height: 1px; width: 40px; background-color: #374151; margin-top: 15px;"></div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #f3f4f6; font-size: 16px; line-height: 24px;">
                                        <p style="margin-top: 0; font-size: 18px; font-weight: 600;">Chào bạn,</p>
                                        <p style="color: #9ca3af;">Đội ngũ quản trị viên CineBook đã nhận được và xem xét kỹ lưỡng góp ý của bạn về chủ đề:</p>
                                        
                                        <!-- Subject Card -->
                                        <div style="background-color: #1f2937; border-radius: 16px; padding: 20px; border-left: 4px solid #dc2626; margin: 25px 0;">
                                            <p style="margin: 0; color: #dc2626; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px;">Chủ đề góp ý</p>
                                            <p style="margin: 0; color: #ffffff; font-size: 16px; font-weight: 600;">{{ $feedbackTitle }}</p>
                                        </div>

                                        <p style="color: #9ca3af; margin-bottom: 15px;">Chúng tôi xin gửi lời cảm ơn chân thành và gửi đến bạn phản hồi chi tiết như sau:</p>

                                        <!-- Message Content -->
                                        <div style="background-color: #030712; border-radius: 20px; padding: 30px; border: 1px solid #374151; color: #ffffff; margin-bottom: 30px;">
                                            <div style="font-size: 16px; line-height: 1.8; color: #e5e7eb;">
                                                {!! nl2br(e($replyMessage)) !!}
                                            </div>
                                        </div>

                                        <p style="color: #9ca3af; margin-bottom: 30px; font-style: italic;">Hy vọng phản hồi này giải đáp được thắc mắc của bạn. Nếu có bất kỳ câu hỏi nào khác, đừng ngần ngại trả lời qua các kênh hỗ trợ chính thức của chúng tôi.</p>
                                        
                                        <!-- Footer Text -->
                                        <p style="margin-bottom: 0;">Trân trọng,</p>
                                        <p style="margin-top: 5px; font-weight: 700; color: #ffffff; font-size: 18px;">Đội ngũ điều hành CineBook</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Secondary Accent -->
                    <tr>
                        <td align="center" style="padding: 0 40px 40px 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #030712; border-radius: 16px;">
                                <tr>
                                    <td align="center" style="padding: 20px; color: #6b7280; font-size: 13px;">
                                        <i style="color: #dc2626;">"Trải nghiệm điện ảnh đỉnh cao, dịch vụ tận tâm."</i>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- System Footer -->
                    <tr>
                        <td align="center" style="background-color: #000000; padding: 25px 40px; border-top: 1px solid #1f2937;">
                            <p style="margin: 0; color: #4b5563; font-size: 12px; line-height: 18px;">
                                Đây là email tự động từ hệ thống CineBook.<br>
                                Vui lòng không trả lời trực tiếp email này.
                            </p>
                            <div style="margin-top: 15px;">
                                <span style="color: #374151; font-size: 12px;">&copy; 2026 CineBook Cinema. All rights reserved.</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

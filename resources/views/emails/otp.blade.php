<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 20px; }
        .container { background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 500px; margin: 0 auto; text-align: center; }
        .otp-code { font-size: 32px; font-weight: bold; color: #ef4444; letter-spacing: 5px; margin: 20px 0; padding: 10px; background-color: #fef2f2; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Xác thực tài khoản CINEBOOK</h2>
        <p>Xin chào,</p>
        <p>Bạn vừa yêu cầu mã OTP để xác thực. Đây là mã của bạn:</p>
        <div class="otp-code">{{ $otp }}</div>
        <p>Vui lòng không chia sẻ mã này với bất kỳ ai để bảo mật tài khoản.</p>
        <p>Trân trọng,<br>Đội ngũ Cinebook</p>
    </div>
</body>
</html>

🚀 HƯỚNG DẪN FIX LỖI GOOGLE LOGIN CHO TEAM CINEBOOK 🚀

Chào anh em! Để giải quyết dứt điểm lỗi cURL 60 (SSL) và lỗi mismatch session khi login Google ở local, sếp Thiên Ân và GRACE đã cập nhật bản vá mới nhất. Anh em làm theo các bước sau nhé:

1. CẬP NHẬT CODE MỚI NHẤT
- Checkout sang nhánh mới: 'feature/fix-google-login' hoặc pull code mới nhất từ sếp Ân.

2. CẤU HÌNH .ENV
- Đảm bảo dòng: APP_ENV=local (Điều kiện để kích hoạt bộ lọc bypass SSL).
- Kiểm tra GOOGLE_REDIRECT_URI phải khớp chính xác với URL đang chạy (localhost:8000 hoặc domain .test).

3. LƯU Ý CHO ANH EM DÙNG HERD 🐘
- Dùng lệnh 'herd secure' trong terminal tại thư mục project để Herd tự cấp SSL "xịn".
- Restart Herd sau khi pull code mới để chắc chắn nhận config mới nhất.

4. BƯỚC QUAN TRỌNG NHẤT: DỌN DẸP COOKIE 🧼
- Nhấn 'Ctrl + Shift + Delete' trên trình duyệt (Chrome/Comet/Edge), chọn 'All time' và xóa sạch 'Cookies and other site data'. 
- Nếu không xóa, session cũ sẽ gây lỗi 419 hoặc Invalid State (dòng 237 AbstractProvider).

5. CHẠY SERVER
- Chỉ cần chạy 'php artisan serve'. 
- GRACE đã cài "bộ tự động dọn dẹp cache" bên trong file artisan. Anh em sẽ thấy dòng chữ thông báo dọn dẹp hiện lên mỗi khi khởi động. Thế là xong!

Chúc anh em code mượt, login bay vèo vèo! 🎉✨

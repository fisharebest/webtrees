<div id="content">
<h3>Về dự án này</h3>
Ý tưởng cho dự án này là của Daniel Schlichtholz.<p>Năm 2004, ông tạo ra diễn đàn <a href="http://forum.mysqldumper.de" target="_blank">MySQLDumper</a> và sau đó, lập trình những đoạn mã mới, bổ sung đoạn mã của Daniel.<br>Sau một thời gian ngắn, mã nguồn phát triển thành một dự án lớn hơn.<p>Nếu bạn có bất cứ góp ý nào nhằm cải tiến mã nguồn, bạn có thể truy cập vào Diễn đàn MySQLDumper: <a href="http://forum.mysqldumper.de" target="_blank">http://forum.mysqldumper.de</a>.<p>Chúc bạn có những giây phút thú vị.<br><br><h4>The MySQLDumper-Team</h4>
<table><tr><td><img src="images/logo.gif" alt="MySQLDumper" border="0"></td><td valign="top">
Daniel Schlichtholz</td></tr></table>

<h3>Trợ giúp về MySQLDumper</h3>

<h4>Download</h4>
Mã nguồn này có thể tải vè từ trang chủ của MySQLDumper.<br>
Hãy ghé thăm trang chủ của MySQLDumper thường xuyên để có những thông tin mới nhất, đầy đủ nhất về các phiên bản nâng cấp của phần mềm.<br>
Địa chỉ website là <a href="http://forum.mysqldumper.de" target="_blank">
http://forum.mysqldumper.de
</a>

<h4>System Mandatories</h4>
Mã nguồn này có thể chạy trên các hệ thống máy chủ thông dụng (Windows, Linux, ...) <br>
và PHP >= Version 4.3.4 với GZip-Library, MySQL (>= 3.23), JavaScript (phải được cho phép).

<a href="install.php?language=vn" target="_top"><h4>Cài đặt</h4></a>
Chương trình có thể cài đặt rất dễ dàng.
Giải nén chương trình vào thư mục bất kỳ trên Webserver<br>
(VD: vào thư mục gốc của Website [Thư mục gốc Server/]MySQLDumper)<br>
chmod 777 cho file config.php<br>
... hết!<br>
Chạy MySQLDumper trên trình duyệt (FireFox, Internet Explorer) bằng cách gõ "http://webserver/MySQLDumper"
để hoàn thành cài đặt (xem hướng dẫn kèm theo).

<br><b>Chú ý:</b><br><i>Nếu webserver của bạn chạy với tùy chọn safemode=ON thì MySqlDump không thể tạo thư mục.<br>
Bạn sẽ phải tự mình tạo.<br>
Trong trường hợp đó, MySqlDump sẽ cho bạn biết mình phải làm gì.<br>
Sau khi bạn tạo ra các thư mục, MySqlDump sẽ hoạt động bình thường.</i><br>

<a name="perl"></a><h4>Hướng dẫn cho mã nguồn Perl</h4>

Hầu hết thì mọi website đều có một thư mục tên là cgi-bin, trong đó Perl có thể chạy. <br>
Điều này có nghĩa là có thể truy cập từ trình duyệt vào một địa chỉ có dạng http://www.domain.de/cgi-bin/. <br><br>

Đọc kỹ các hướng dẫn từng bước ở dưới đây.  <br><br>

	
1. Vào MySQLDumper/ trang "Sao lưu"; bấm vào "Backup Perl" <br> 
2. Sao chép đường dẫn đứng đằng sau mục nhập trong crondump.pl cho $absolute_path_of_configdir: <br>
3. Mở tập tin "crondump.pl" bằng trình soạn thảo <br>
4. Dán đường dẫn đã sao chép với absolute_path_of_configdir (không có khoảng trắng) <br>
5. Lưu crondump.pl <br>
6. sao chép crondump.pl, cũng như perltest.pl và simpletest.pl vào thư mục cgi-bin (chế độ ASCII trong chương trình ftp!) <br>
7. chmod 755 cho nó. <br>
7b. Nếu muốn phần mở rộng là cgi, chỉ việc đổi tên đuôi pl -> cgi<br>
8. Vào MySQLDumper / Cấu hình <br>
9. Nhấp chuột vào <br> Cronscript
10. Thay đổi đường dẫn Perl đến /cgi-bin/ <br>
10B. Nếu Script được đổi tên thành *. cgi, đổi Fileextension thành cgi <br>
11. Ghi lại cấu hình. <br><br>

Sẵn sàng! Các lệnh có sẵn ở trang "Sao lưu" <br><br>

Bạn có thể chạy Perl ở bất cứ đâu, chỉ cần thực hiện các bước sau:  <br><br>

1.  Vào MySQLDumper / "Sao lưu".  <br>
2.  Sao chép đường dẫn đứng đằng sau mục nhập trong crondump.pl cho $absolute_path_of_configdir:  <br>
3. Mở file "crondump.pl" <br>
4. án đường dẫn đã sao chép với absolute_path_of_configdir (không có khoảng trắng) <br>
5.  Save crondump.pl <br>

6. chmod 755 cho nó.  <br> 
6b. Nếu muốn phần mở rộng là cgi, chỉ việc đổi tên đuôi pl -> cgi <br>
(tiếp: 10b+11 như trên) <br><br>


Người dùng Windows phải đổi tất cả các dòng đầu của mã nguồn Perl, thành đường dẫn tới Perl.  <br><br>

Ví dụ:  <br>

instead of:  #!/usr/bin/perl w <br>
now #!C:\perl\bin\perl.exe w<br>

<h4>Hệ thống</h4><ul>

<h6>Menu</h6>
Hộp chọn cho phép bạn chọn CSDL để làm việc.<br>
Tất cả các thao tác sẽ được áp dụng cho CSDL đang hiển thị.

<h6>Trang chủ</h6>
Tại đây bạn có thể xem tất cả các thông tin của hệ thống, phiên bản của phần mềm và chi tiết các cấu hình của hệ thống.<br>
Khi bạn Click vào một CSDL trong bảng, bạn sẽ thấy danh sách các bảng với số bản ghi, kích cỡ và lần cuối cập nhật stamp.

<h6>Cấu hình</h6>
Tại đây bạn có thể sửa các cấu hình, ghi hoặc nạp các cấu hình mặc định.
<ul>
	<li><a name="conf1"><strong>Cấu hình CSDL:</strong> Danh scahs của cấu hình CSDL. CSDL đang hoạt động được tô đậm.</li>
	<li><a name="conf2"><strong>Table-Prefix (tiền tố bảng):</strong> bạn có thể chọn 1 tiền tố (prefix) cho mỗi CSDL. Tiền tố là một dạng lọc, nó chỉ thao tác với các bảng trong một khu vực, bắt đầu bằng tiền tố này (vd: tất cả các bagr bắt đầu bằng "phpBB_"). Nếu bạn không muốn sử dụng nó, hãy để trống trường này.</li>
	<li><a name="conf3"><strong>Nén GZip:</strong> Dùng để kích hoạt chế độ nén. Hãy sử dụng nếu có thể vì nó giúp bạn nén nhỏ file, tiết kiệm dung lượng host, giảm thời gian download và do đó tiết kiệm băng thông.</li>
	<li><a name="conf19"></a><strong>Số bản ghi khi Sao lưu:</strong> Đây là số của các bản ghi được đọc cùng lúc trong khi sao lưu, trước khi gọi các tập lệnh. Nếu nó làm chậm máy chủ, bạn có thể giảm tham số này để ngăn ngừa timeouts.</li>
	<li><a name="conf20"></a><strong>Số bản ghi khi phục hồi:</strong> Đây là số của các bản ghi được đọc cùng lúc trong khi sao lưu, trước khi gọi các tập lệnh. Nếu nó làm chậm máy chủ, bạn có thể giảm tham số này để ngăn ngừa timeouts.</li>
	<li><a name="conf4"></a><strong>Thư mục chứa file Backup:</strong> Chọn thư mục chứa file Backup. Nếu thư mục này chưa có, hệ thống sẽ tạo nó cho bạn. Có thể sử dụng đường dẫn tương đối hoặc tuyệt đối.</li>
	<li><a name="conf5"></a><strong>Gửi file sao lưu vào email:</strong> Cho phép hệ thống gửi một email đính kèm file backup tới địa chỉ email được chỉ ra bên dưới (cẩn trọng khi sử dụng!, bạn phải chọn tùy chọn nén file khi sử dụng tính năng này vì file quá lớn có thể không gửi vào email được!)</li>
	<li><a name="conf6"></a><strong>Địa chỉ Email:</strong> Recipient's email address</li>
	<li><a name="conf7"></a><strong>Tiêu đề của Email:</strong> Tóm lược nội dung email bằng một tiêu dề.</li>
	<li><a name="conf13"></a><strong>FTP-Transfer: </strong>Tùy chọn này cho phép hệ thống tự động gửi file backup bằng phương thức FTP.</li>
	<li><a name="conf14"><strong>FTP Server: </strong>Địa chỉ của FTP-Servers (VD: ftp.mangvn.org)</li>
	<li><a name="conf15"></a><strong>FTP Server Port: </strong>cổng kết nối FTP-Server (mặc định là cổng 21)</li>
	<li><a name="conf16"></a><strong>FTP User: </strong>tên đăng nhập tài khoản FTP</li>
	<li><a name="conf17"></a><strong>FTP Passwort: </strong>Mật khẩu  đăng nhập tài khoản FTP</li>
	<li><a name="conf18"></a><strong>FTP Upload-Ordner: </strong>thư mục chứa file backup (phải được cho phép upload lên!)</li>
	
	<li><a name="conf8"></a><strong>Tự động xóa file backup:</strong> Tùy chọn này cho phép tự động xóa theo một quy luật được thiết lập trước.</li>
	<li><a name="conf10"></a><strong>Xóa file nếu số lượng vượt quá:</strong> Nếu các file có số lương nhiều hơn giá trị được chỉ ra thì file cũ sẽ bị xóa.</li>
	<li><a name="conf11"></a><strong>Language (ngôn ngữ):</strong> choose your language for the interface (chọn ngôn ngữ bạn muốn sử dụng cho chương trình này).</li>
</ul>

<h6>Quản lý</h6>
Danh sách tất cả các thao tác có thể thực hiện sẽ được liệt kê tại đây.<br>
Bạn có thể thấy tất cacsr các file trong thư mục Backup.
Thao tác "Phục hồi" và "Xóa" có thể thực hiện ở trước mỗi file.
<UL>
	<li><strong>Phục hồi:</strong> bạn có thể phục hồi các bản ghi của file backup đã lựa chọn.</li>
	<li><strong>Xóa:</strong> bạn có thể xóa file backup đã lựa chọn.</li>
	<li><strong>Bắt đầu 1 sao lưu (Dump):</strong> tại đây bạn có thể bắt đầu 1 sao lưu (dump) mới với các thông số đã được cấu hình.</li>
</UL>

<h6>Log / Nhật ký hệ thống</h6>
Bạn có thể đọc các bản ghi nhật ký và xóa chúng.

<h6>Yêu cầu / Trợ giúp</h6>
(chính là trang này.)
</ul>
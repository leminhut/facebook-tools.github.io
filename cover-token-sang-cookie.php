<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Document</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
</head>
<body style="padding-top: 50px;">
	<div class="container">
		<div class="panel panel-primary">
			<div class="panel-heading">Get Cookies from Access Token</div>
			<div class="panel-body">
				<textarea  id="access_token" rows="15" class="form-control" placeholder="Nhập list Access Token ..."></textarea>
				<div class="col-md-12 text-center" style="font-size: 50px;">
					<div class="col-md-6" style="color: green" id="success">0</div>
					<div class="col-md-6" style="color: red" id="error">0</div>
				</div>
				<br>
				<select id="option" class="form-control">
					<option value="editthiscookies">Export dạng Edit This Cookies</option>
					<option value="base64">Export dạng base64</option>
					<option value="base64_long">Export dạng base64 (name=value;)</option>
					<option value="semicolon">Export dạng name=value;</option>
				</select>
			</div>
			<div class="panel-footer">
				<div class="text-center">
					<button class="btn btn-primary" id="submit" data-loading-text="Đang gửi ...">Bắt đầu</button>
				</div>
			</div>
		</div>
		<div class="panel panel-primary">
			<div class="panel-heading">Kết Quả</div>
			<div class="panel-body">
				<textarea  id="output_access_token" rows="10" class="form-control" disabled="" placeholder="Chưa có gì !"></textarea>
			</div>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			var access_token, option, success, error;
			$("#submit").click(function() {
				access_token = $("#access_token").val().trim().split("\n");
				option       = $("#option").val();
				success      = 0;
				error        = 0;
				$("#submit").button('loading');
				run_script(0);
			});
			function run_script(index) {
				if (index < access_token.length) {
					get_cookies(index);
				} else {
					$("#submit").button('reset');
					$("#output_access_token").removeAttr('disabled');
				}
			}
			function get_cookies(index) {
				$.get('https://graph.facebook.com/app', {
					access_token: access_token[index]
				}).done(function(e) {
					$.get('https://api.facebook.com/method/auth.getSessionforApp', {
						access_token: access_token[index],
						format: 'json',
						new_app_id: e.id,
						generate_session_cookies: '1'
					}).done(function(e) {
						if (e.uid) {
							var text = '';
							if (option == 'editthiscookies') {
								text = JSON.stringify(e.session_cookies);
							}
							if (option == 'base64') {
								var c_user = $.grep(e.session_cookies, function(c) {
									return c.name == 'c_user';
								});
								var xs     = $.grep(e.session_cookies, function(c) {
									return c.name == 'xs';
								});
								text = btoa(decodeURIComponent(c_user[0].value + '|' + xs[0].value));
							}
							if (option == 'base64_long') {
								var ss = '';
								e.session_cookies.forEach(function(item) {
									ss += item.name + '=' + item.value + ';';
								});
								text = btoa(ss);
							}
							if (option == 'semicolon') {
								var ss = '';
								e.session_cookies.forEach(function(item) {
									ss += item.name + '=' + item.value + ';';
								});
								text = ss;
							}
							$("#output_access_token").append(text + "\n");
							++success;
							$("#success").text(success);
						} else {
							++error;
							$("#error").text(error);
						}
					}).error(function(e) {
						++error;
						$("#error").text(error);
					}).always(function() {
						run_script(index + 1);
					});
				}).error(function() {
					++error;
					$("#error").text(error);
					run_script(index + 1);
				});
			}
		});
	</script>
</body>
</html>
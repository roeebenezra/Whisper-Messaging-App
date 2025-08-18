<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/plyr/3.7.8/plyr.min.css" />

	<link rel="stylesheet" href="./assets/css/main.css?v=<?php echo time(); ?>" />
	<link rel="stylesheet" href="./assets/css/mobile.css?v=<?php echo time(); ?>" />
	<link rel="stylesheet" href="./assets/css/login.css?v=<?php echo time(); ?>" />

	<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
	<script src="/assets/js/emoji_picker_init.js"></script>

	<title>Whisper Messaging App</title>
</head>

<body>
	<!-- Login container -->
	<div id="login_container" class="wm-wrap">
		<form id="login_form" autocomplete="off" class="wm-card">
			<div class="wm-header">
				<div class="wm-logo" aria-hidden="true">💬</div>
				<h1 class="wm-app">Whisper Messaging App</h1>
				<h2 class="wm-title">Sign in</h2>
				<p class="wm-sub">Secure one-time code login</p>
			</div>

			<!-- Inline error (optional) -->
			<div id="wm-error" class="wm-error" style="display:none;"></div>

			<label class="wm-label" for="login_username">Username</label>
			<input type="text" id="login_username" name="username" class="wm-input" placeholder="Enter your username"
				required autocomplete="username" />

			<!-- Honeypot (unchanged) -->
			<div style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;">
				<label>Website</label>
				<input type="text" id="website" name="website" value="" />
			</div>

			<button type="button" id="btn_send_otp" class="wm-btn wm-btn-primary">Send OTP</button>

			<div class="wm-otp-block" id="login_otp_group" style="display:none;">
				<label class="wm-label" for="login_otp">One-time code</label>
				<input type="text" id="login_otp" name="otp" class="wm-input wm-otp" placeholder="••••••" maxlength="6"
					inputmode="numeric" autocomplete="one-time-code" />
			</div>

			<button type="button" id="btn_verify_otp" class="wm-btn wm-btn-success" style="display:none;">
				Verify &amp; Sign in
			</button>

			<div class="wm-foot">
				<small>Protected login • OTP required</small>
			</div>
		</form>
	</div>


	<div id="main" class="main-container row" style="display:none;">
		<div id="chats_list" class="left-container col-md-4">
			<!--header -->
			<div class="header row">
				<div class="col-12 row">
					<div class="user_avatar_container col-2">
						<img src="./profile_pics/assaf.jpg" alt="User's Avatar" />
					</div>
					<div class="user_info_container col-6">
						<div class="user_full_name_comes_here">Assaf Levy</div>
						<div class="user_status_comes_here hide_on_mobile">Online</div>
					</div>
					<div class="logout_btn_container col-4">
						<button class="logout btn btn-dark">Logout</button>
					</div>
				</div>
			</div>
			<!--search-container -->
			<div class="search-container">
				<div class="input">
					<i class="fa-solid fa-magnifying-glass"></i>
					<input type="text" placeholder="Search or start new chat   " />
				</div>
				<i class="fa-sharp fa-solid fa-bars-filter"></i>
			</div>
			<!--chats -->
			<div id="chats" class="chat-list"></div>
		</div>

		<div id="chat_window" class="right-container col-md-8">
			<!--header -->
			<div class="header row">

				<div class="row col-10">
					<div class="show_chats_list col-2">
						<i class="fa-solid fa-chevron-left"></i>
					</div>

					<div class="contact_profile_img col-3">
						<img class="dp" src="" alt="" />
					</div>

					<div class="contact_name_container col-7">
						<span class="contact_name"></span>
						<span class="contact_id"></span>
					</div>
				</div>

				<div class="contact_more_options col-2">
					<ul class="row">
						<li class="col-6 show_more_option_menu">
							<i class="fa-solid fa-ellipsis-vertical"></i>
						</li>
					</ul>
				</div>

			</div>
			<!--chat-container -->
			<div id="msgs" class="chat-container"></div>
			<!--input-bottom -->
			<form id="send_msg" class="send_msg_form chatbox-input">
				<i class="fa-sharp fa-solid fa-paperclip"></i>
				<input id="msg" type="text" placeholder="Type a message" required />
				<button class="submit_msg">
					<i class="fa-solid fa-paper-plane"></i>
				</button>
			</form>
		</div>
	</div>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/plyr/3.7.8/plyr.min.js"></script>

	<script src="./assets/js/main.secure.js.php?v=<?php echo time(); ?>"></script>
	<script src="./assets/js/login.js?v=<?php echo time(); ?>"></script>

</body>

</html>
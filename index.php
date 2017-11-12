<?php
$session = mt_rand(1,999);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Chat</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<script src="js/jquery.js" type="text/javascript"></script>
	<style type="text/css">
	* {margin:0;padding:0;box-sizing:border-box;font-family:arial,sans-serif;resize:none;}
	html,body {width:100%;height:100%;}
	#wrapper {position:relative;margin:auto;max-width:1000px;height:100%;}
	#chat_output {position:absolute;top:0;left:0;padding:20px;width:100%;height:calc(100% - 100px);}
	#chat_input {position:absolute;bottom:0;left:0;padding:10px;width:100%;height:100px;border:1px solid #ccc;}
	#user_typing {font-style: italic; color: #979797; font-size: 12px;}
	</style>
</head>
<body>
	<div id="wrapper">
		<div id="chat_output"></div>
		<span id="user_typing" class="typing"></span>
		<textarea id="chat_input" placeholder="Deine Nachricht..."></textarea>
		<script type="text/javascript">
		jQuery(function($){
			var typing = {status: false, time:0, prev:0};
			// Websocket
			var websocket_server = new WebSocket("ws://localhost:8080/");
			websocket_server.onopen = function(e) {
				websocket_server.send(
					JSON.stringify({
						'type':'socket',
						'user_id': '<?=$session?>'
					})
				);
			};
			websocket_server.onerror = function(e) {
				// Errorhandling
			}
			websocket_server.onmessage = function(e)
			{
				var json = JSON.parse(e.data);
				switch(json.type) {
					case 'chat':
						$('#chat_output').append(json.msg);
						console.log(json.socket);
						break;

					case 'dsconn':
						$('#chat_output').append(json.msg);
						break;

					case 'typing':
						$('#user_typing').html(json.msg);
						chekTyping();					
						break;
				}
			}
			// Events
			$('#chat_input').on('keyup',function(e){
				if(e.keyCode==13 && !e.shiftKey)
				{
					var chat_msg = $(this).val();
					websocket_server.send(
						JSON.stringify({
							'type':'chat',
							'user_id':<?=$session?>,
							'chat_msg':chat_msg
						})
					);
					$(this).val('');
				}

			});

			$('#chat_input').on('keypress',(function(event) {
				websocket_server.send(
						JSON.stringify({
							'type':'typing',
							'user_id':<?=$session?>
						})
					);
			}))

			function chekTyping () {
				//typing.staus = false;
				setTimeout(function () {
					//if (typing.prev < typing.time) {
						$('#user_typing').html("");
					//}
				},2000);
			}

		});
		</script>
	</div>
</body>
</html>
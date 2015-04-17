<div class="wrap">
	<h2>Documentation</h2>

		<h3>Requirements</h3>
		<ol>
			<li><strong>Sockets</strong> - Your webhost must have the Socketing function enabled and opened for incoming and outgoing connections for the specific ports ( example: 25565 - for minecraft servers / 27015 - for Counter Strike servers )</li>
		</ol>

		<h3>Shortcodes</h3>
		Make sure you have at least 1 server added to the database before trying to add a shortcode !

			<h4>Basic Shortcode use</h4>
			<p>You must specify the <strong>server_id</strong> of the server you want to get details.You can find the server_id in the Game Servers list in the admin panel.</p>
			
			<code>
			[game-servers server_id="1"]
			</code>

			<h4>Get specific details of a server</h4>
			<p>If you want to get a specific data from a server you will need to specify the <strong>display</strong> parameter.</p>

			<code>
			[game-servers server_id="1" display="server_name"]
			</code>

			<p>That will only output the name of the server.Possible values for the <strong>display</strong> parameter:</p>
			<ul>
				<li>server_name</li>
				<li>server_status</li>
				<li>server_address</li>
				<li>server_online_players</li>
				<li>server_maximum_online_players</li>
			</ul>

		<h3>FAQ's</h3>
		<ul>
			<li><strong>I can't add a server, it keeps saying it is offline but the server is actually online</strong></li>
			<li>Please read the above requirements and contact your webhost provider about it.</li>

			<li><strong>What games does the 'source' engine support ?</strong></li>
			<li>The source engine supports games made with Source & GoldSource engine.For example: Team Fortress, Counter Strike, Rust, Left 4 Dead..etc) You can google to find an exact list of supported games.</li>
		</ul>

		
		<h3>Contact</h2>
		<p>You can contact me for support or custom work by email or by skype</p>
		<ul>
			<li><strong>Skype:</strong> neeesteea.soda</li>
			<li><strong>Email:</strong> gfabruno@gmail.com</li>
			<li><strong>Portfolio:</strong> <a href="http://codecanyon.net/user/GrohsFabian">Codecanyon</a></li>
			<li><strong>Website:</strong> <a href="http://grohsfabian.com">GrohsFabian.com</a></li>
			<li><strong>Twitter:</strong> <a href="http://twitter.com/grohsfabian">@GrohsFabian</a></li>
		</ul>


</div>
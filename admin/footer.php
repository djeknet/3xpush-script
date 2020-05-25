<?php
// 3xpush Script - Push Subscription Management System 
// Copyright 2020 Evgeniy Orel
// Site: https://script.3xpush.com/
// Email: script@3xpush.com
// Telegram: @Evgenfalcon
//
// ======================================================================
// This file is part of 3xpush Script.
//
// 3xpush Script is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// 3xpush Script is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with 3xpush Script.  If not, see <https://www.gnu.org/licenses/>.
//======================================================================

if(count(get_included_files()) ==1) exit("Direct access not permitted.");

?>
</div>
			<footer class="footer">
				<div class="container-fluid">
					<nav class="pull-left">
						<ul class="nav">
							<li class="nav-item">
								<a class="nav-link" href="?m=rules">
									<?php echo _RULES; ?>
								</a>
							</li>
						</ul>
					</nav>
					<div class="copyright ml-auto">
						2020 <?php echo $settings['sitename']; ?>
					</div>				
				</div>
			</footer>
		</div>
	</div>


<?php
if (is_array($page_speed) && $dev==1){
    echo "<div align=left><hr>Times:<br>";
     foreach ($page_speed as $num => $value) {
        $time = $value[1] - $value[0];
        echo "$num: $time sek. <br>";
     }
     echo '</div>';
}
?>




<?php
if ($config['local_proj']!=1) {

echo content_name('metriks', 'code');   
echo content_name('scripts', 'code');   
}

// отправляем оповещения об ошибках
if (is_array($errors)) {
    $errors = json_encode($errors);
  if ($errors!="[null]") jset(1, ''.$errors.'', 1);  
}

?>


	
	<script src="assets/js/core/popper.min.js"></script>
	<script src="assets/js/core/bootstrap.min.js"></script>
	<!-- jQuery UI -->
	<script src="assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
	<script src="assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>
	<!-- Bootstrap Toggle -->
	<script src="assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>
	<!-- jQuery Scrollbar -->
	<script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
	<!-- Atlantis JS -->
	<script src="assets/js/atlantis.min.js"></script>
  
    
</body>

</html>

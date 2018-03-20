<!DOCTYPE html>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Supervisord Monitoring</title>
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<link href="<?php echo base_url('/css/bootstrap.min.css');?>" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url('/css/bootstrap-responsive.min.css');?>" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url('/css/custom.css');?>" rel="stylesheet" type="text/css">
	<script src="%3C?php%20echo%20base_url('/js/jquery-1.10.1.min.js');?%3E" type="text/javascript">
	</script>
	<script src="%3C?php%20echo%20base_url('/js/bootstrap.min.js');?%3E" type="text/javascript">
	</script><noscript><?php
	        if($this->config->item('refresh')){ ?>
	<meta content="<?php echo $this->config->item('refresh');?>" http-equiv="refresh"><?php } ?></noscript>
	<style>
	      span.pull-right {
	          display: none;
	      }
	</style>
</head>

<body>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<button class="btn btn-navbar" data-target=".nav-collapse" data-toggle="collapse" type="button"><span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span></button> <a class="brand" href="%3C?php%20echo%20site_url('');?%3E">Supervisord Monitoring</a>

				<div class="nav-collapse collapse">
					<ul class="nav">
						<li class="active">
							<a href="%3C?php%20echo%20site_url();?%3E">Home</a>
						</li>


						<li>
							<a href="?mute=%3C?php%20echo%20($muted?-1:1);?%3E"><i class="icon-music icon-white"></i>&nbsp;<?php
							                                        if($muted){
							                                            echo "Unmute";
							                                        }else{
							                                            echo "Mute";
							                                        }
							                                    ;?></a>
						</li>


						<li>
							<a href="%3C?php%20echo%20site_url();?%3E">Refresh <b id="refresh">(<?php echo $this->config->item('refresh');?>)</b> &nbsp;</a>
						</li>
					</ul>
				</div>
				<!--/.nav-collapse -->
			</div>
		</div>
	</div>


	<div class="container">
		<?php
		                if($muted){
		                    echo '<div class="row"><div class="span4 offset4 label label-important" style="padding:10px;margin-bottom:20px;text-align:center;">';
		                    echo 'Sound muted for '.timespan(time(),$muted).' <span class="pull-right"><a href="?mute=-1" style="color:white;"><i class="icon-music icon-white"></i> Unmute</a></span></div></div>';
		                }
		            
		                ?>

		<div class="row">
			<?php
			                            $alert = false;
			                            foreach($list as $name=>$procs){
			                                $parsed_url = parse_url($cfg[$name]['url']);
			                                if ( isset($cfg[$name]['username']) && isset($cfg[$name]['password']) ){
			                                    $base_url = 'http://' . $cfg[$name]['username'] . ':' . $cfg[$name]['password'] . '@';
			                                }else{
			                                    $base_url = 'http://';
			                                }
			                                $ui_url = $base_url . $parsed_url['host'] . ':' . $cfg[$name]['port']. '/';
			                            ?>

			<div class="span&lt;?php echo ($this-&gt;config-&gt;item('supervisor_cols')==2?'6':'4');?&gt;">
				<table class="table table-bordered table-condensed table-striped">
					<tr>
						<th colspan="4">
							<a href="%3C?php%20echo%20$ui_url;%20?%3E"><?php echo $name; ?></a> <?php if($this->config->item('show_host')){ ?><i><?php echo $parsed_url['host']; ?></i><?php } ?> <?php
							                                                    if(isset($cfg[$name]['username'])){echo '<i class="icon-lock icon-green" style="color:blue" title="Authenticated server connection"></i>';}
							                                                    echo '&nbsp;<i>'.$version[$name].'</i>';
							                                                    if(!isset($procs['error'])){
							                                                    ?> <span class="server-btns pull-right"><a class="btn btn-mini btn-inverse" href="%3C?php%20echo%20site_url('/control/stopall/'.$name);%20?%3E" type="button"><i class="icon-stop icon-white"></i> Stop all</a> <a class="btn btn-mini btn-success" href="%3C?php%20echo%20site_url('/control/startall/'.$name);%20?%3E" type="button"><i class="icon-play icon-white"></i> Start all</a> <a class="btn btn-mini btn-primary" href="%3C?php%20echo%20site_url('/control/restartall/'.$name);%20?%3E" type="button"><i class="icon icon-refresh icon-white"></i> Restart all</a></span> <?php
							                                                    }
							                                                    ?>
						</th>
					</tr>
					<?php
					                                        $CI = &get_instance();
					                                        foreach($procs as $item){

					                                            if($item['group'] != $item['name']) $item_name = $item['group'].":".$item['name'];
					                                            else $item_name = $item['name'];
					                                            
					                                            $check = $CI->_request($name,'readProcessStderrLog',array($item_name,-1000,0));
					                                            if(is_array($check)) $check = print_r($check,1);
					                                            
					                                            if(!is_array($item)){
					                                                    // Not having array means that we have error.
					                                                    echo '<tr><td colspan="4">'.$item.'</td></tr>';
					                                                    echo '<tr><td colspan="4">For Troubleshooting <a href="https://github.com/mlazarov/supervisord-monitor#troubleshooting" target="_blank">check this guide</a></td></tr>';
					                                                    continue;
					                                            }

					                                            $pid = $uptime = '&nbsp;';
					                                            $status = $item['statename'];
					                                            if($status=='RUNNING'){
					                                                $class = 'success';
					                                                list($pid,$uptime) = explode(",",$item['description']);
					                                            }
					                                            elseif($status=='STARTING') $class = 'info';
					                                            elseif($status=='FATAL') { $class = 'important'; $alert = true; }
					                                            elseif($status=='STOPPED') $class = 'inverse';
					                                            else $class = 'error';

					                                            $uptime = str_replace("uptime ","",$uptime);
					                                            ?>

					<tr>
						<td><?php
						                                                        echo $item_name;
						                                                        if($check){
						                                                            $alert = true;
						                                                            echo '<span class="pull-right"><a href="'.site_url('/control/clear/'.$name.'/'.$item_name).'" id="'.$name.'_'.$item_name.
						                                                                    '" onclick="return false" data-toggle="popover" data-message="'.htmlspecialchars($check).'" data-original-title="'.
						                                                                    $item_name.'@'.$name.'" class="pop btn btn-mini btn-danger"><img src="' . base_url('/img/alert_icon.png') . '" /></a></span>';
						                                                        }
						                                                        ?>
						</td>

						<td width="10"><span class="label label-&lt;?php echo $class;?&gt;"><?php echo $status;?></span>
						</td>

						<td style="text-align:right" width="80"><?php echo $uptime;?>
						</td>

						<td style="width:1%">
							<!--div class="btn-group">
                                    <button class="btn btn-mini">Action</button>
                                    <button class="btn btn-mini dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="test">Restart</a></li>
                                        <li><a href="zz">Stop</a></li>
                                    </ul>
                                </div//-->


							<div class="actions">
								<?php if($status=='RUNNING'){ ?><a class="btn btn-mini btn-inverse" href="%3C?php%20echo%20site_url('/control/stop/'.$name.'/'.$item_name);?%3E" type="button"><i class="icon-stop icon-white"></i></a> <a class="btn btn-mini btn-inverse" href="%3C?php%20echo%20site_url('/control/restart/'.$name.'/'.$item_name);?%3E" type="button"><i class="icon-refresh icon-white"></i></a> <?php } if($status=='STOPPED' || $status == 'EXITED' || $status=='FATAL'){ ?> <a class="btn btn-mini btn-success" href="%3C?php%20echo%20site_url('/control/start/'.$name.'/'.$item_name);?%3E" type="button"><i class="icon-play icon-white"></i></a> <?php } ?>
							</div>
						</td>
					</tr>
					<?php
					                                        }

					                                        ?>
				</table>
			</div>
			<?php
			                            }
			                            if($alert && !$muted && $this->config->item('enable_alarm')){
			                                echo '<embed height="0" width="0" src="'.base_url('/sounds/alert.mp3').'">';
			                            }
			                            if($alert){
			                                echo '<title>!!! WARNING !!!</title>';
			                            }else{
			                                echo '<title>Support center</title>';
			                            }
			                            
			                            ?>
		</div>
	</div>
	<!-- /container -->


	<div class="footer">
		<p>Powered by <a href="https://github.com/HOAExpress/supervisord-monitor" target="_blank">Supervisord Monitor</a> | Page rendered in <strong>{elapsed_time}</strong> seconds</p>
	</div>
	<script>
	      function show_content($param){
	          stopTimer();
	          $time = new Date();
	          $message = $(this).data('message')+"\n"+$time+"\n"+$time.toDateString();
	          $title = $(this).data('original-title');
	          $content = '<div class="well" style="padding:20px;">'+nl2br($message)+'<\/div>';
	          $content+= '<div class="pull-left"><form method="get" action="<?php echo $this->config->item('redmine_url');?>" style="display:inline" target="_blank">';
	          $content+= '<input type="hidden" name="issue[subject]" value="'+$title+'"/>';
	          $content+= '<input type="hidden" name="issue[description]" value="'+$message+'"/>';
	          $content+= '<input type="hidden" name="issue[assigned_to_id]" value="<?php echo $this->config->item('redmine_assigne_id');?>"/>';
	          $content+= '<input type="submit" class="btn btn-small btn-inverse" value="Start New Ticket"/>';
	          $content+= '<\/form><\/div>';
	          $content+= '<div class="pull-right"><a href="#" onclick="$(\'#'+$(this).attr('id')+'\').popover(\'hide\');startTimer();" class="btn btn-small btn-primary">ok<\/a>&nbsp;&nbsp;';
	          $content+= '<a href="'+$(this).attr('href')+'" class="btn btn-small btn-danger">Clear<\/a> &nbsp; <\/div>';
	          return $content;
	      }
	      $('.pop').popover(
	          {content: show_content,html:true,placement: 'bottom'}
	      );
	      var $refresh = <?php echo $this->config->item('refresh');?>;
	      var $timer = false;
	      $(window).load(function() {
	          if($refresh > 0) {
	              startTimer();
	          }
	      });
	      function stopTimer(){
	          $('#refresh').html('(p)');
	          clearInterval($timer);
	      }
	      function startTimer(){
	          if($timer)  stopTimer();
	          $timer = setInterval(timer,999);
	      }
	      function timer(){
	          $refresh--;
	          $('#refresh').html('('+$refresh+')');
	          if($refresh<=0){
	              stopTimer();
	              location.href="<?php echo site_url() ?>";
	          }
	          
	      }
	      function nl2br (str, is_xhtml) {
	          var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>';
	          return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
	      }
	</script>
</body>
</html>

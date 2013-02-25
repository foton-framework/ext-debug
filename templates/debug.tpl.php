<?php
//--------------------------------------------------------------------------

function _debug_level($level)
{
	static $levels = array(
		SYS_PHP   => 'PHP',
		SYS_DB    => 'DB',
		SYS_DEBUG => 'DEBUG',
		SYS_USER  => 'USER'
	);
	
	return isset($levels[$level]) ? $levels[$level] : $level;
}

function _debug_benchmark($a, $b)
{
	$a = explode(' ', $a);
	$b = explode(' ', $b);

	return number_format((float)$b[0] - (float)$a[0] + (int)$b[1] - (int)$a[1], 4);
}

function _debug_parse_path($file)
{
	$file = str_replace(array(
		COM_PATH,
		TPL_PATH,
		EXT_PATH,
		APP_PATH,
		SYS_PATH,
		ROOT_PATH,
	), array(
		'Component|',
		'Template|',
		'Extension|',
		'Application|',
		'System|',
		'Root|',
	), $file);
	
	return explode('|', $file);
}

function _debug_get_path()
{
	if (isset($_GET['debud_set_path']))
	{
		setcookie('debud_set_path', $_GET['debud_set_path'], 0, '/');
		$_COOKIE['debud_set_path'] = $_GET['debud_set_path'];
	}
	
	return isset($_COOKIE['debud_set_path']) ? $_COOKIE['debud_set_path'] : '';
}

function _debug_var(&$var, $path = '')
{
	static $show_path;
	
	if ($show_path === NULL)
	{
		$show_path = _debug_get_path();
	}
	
	$type = gettype($var);
	
	$result = '';
	
	if (in_array($type, array('object', 'array')))
	{
		$gr_result = '';
		foreach ($var as $k => &$v)
		{
			$_path = $path . ($path ? ($type=='array' ? "[{$k}]" : "->$k") : $k);
			
			if ($show_path)
			{
				//echo "$show_path<br>$_path<hr>";
				if ($path && ! (strpos($show_path, $path) === 0)) continue; 
			}
			elseif ($path)
			{
				continue;
			}
			$gr_result .= "<tr><td class='foton_col1'><a href='?debud_set_path={$_path}'>{$k}</a></td><td class='foton_col2'>" . _debug_var($v, $_path) . "</td></tr>";
		}
		if ($type == 'object')
		{
			$methods = get_class_methods($var);
			foreach ($methods as $m)
			{
				if ($show_path)
				{
					//echo "$show_path<br>$_path<hr>";
					if ($path && ! (strpos($show_path, $path) === 0)) continue; 
				}
				elseif ($path)
				{
					continue;
				}
				$gr_result .= "<tr><td class='foton_col1'>{$m}()</td><td class='foton_col2'>function</td><td></td></tr>";
			}
		}
		if ($path)      $result .= "$type</td><td class='foton_col3'>";
		if ($gr_result) $result .= "<table>$gr_result</table>";
		else $result .= "";
	}
	else
	{
		
		switch ($type)
		{
			case '_LINK_' : $out_var = '_LINK_'; break;
			case 'boolean': $out_var = $var ? 'TRUE' : 'FALSE'; break;
			case 'NULL'   : $out_var = 'NULL'; break;
			default       : $out_var = htmlspecialchars($var);
		}
		$result .= $type . "</td><td class='foton_{$type}' title='{$path}'><pre>{$out_var}</pre>";
//		$result .= $type . "</td><td>";
		
	}

	return $result;
}

//--------------------------------------------------------------------------
?>

<? $this->head_begin() ?>
<style type="text/css">
.foton_debug {
	clear: both;
	background:#EEE !important;
	color:#333 !important;
	text-shadow: 0 1px 0 #FFF;
	border: 1px solid #FFF;
	margin: 20px 10px;
	padding: 5px;
	font:normal 11px Verdana, "Trebuchet MS", sans-serif;
	-moz-border-radius:10px;
	-moz-box-shadow:0 0 10px rgba(0,0,0,.5);
	-webkit-border-radius:5px;
	-webkit-box-shadow:0 0 10px rgba(0,0,0,.5);
	border-radius:10px;
	box-shadow:0 0 10px rgba(0,0,0,.5);
}
.foton_debug a {
	color:#09F;
	text-decoration: none;
}
.foton_debug a:hover {
	text-decoration: underline;
}
.foton_debug h5 {
	margin: 0 0 3px !important;
	padding: 0 !important;
	font-weight: normal;
	color: #FFF;
	text-shadow: 0 1px 0 rgba(0,0,0,.3);
}
.foton_debug em {
	display: block;
	text-align: center;
	color: #999;
	font-size: 10px;
}
.foton_debug hr {
	border: none !important;
	border-top:1px dotted #900 !important;
}
.foton_debug pre {
	margin: 0;
	font:11px Monaco, serif;
}
.foton_debug div {
	overflow: auto;
	background: #999;
	border: 1px solid #FFF;
	padding: 3px;
	margin:0 0 5px;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	border-radius:5px;
}
.foton_debug table {
	font-size: 11px;
	border-collapse: collapse;
	width: 100%;
	background: #EEE;
	border: 1px solid #FFF;
	-moz-box-shadow:0 0 1px rgba(0,0,0,.3);
	-webkit-box-shadow:0 0 1px rgba(0,0,0,.3);
	box-shadow:0 0 1px rgba(0,0,0,.3);
}
.foton_debug table table {
	margin:2px;
	-moz-box-shadow:0 0 3px rgba(0,0,0,.3);
	-webkit-box-shadow:0 0 3px rgba(0,0,0,.3);
	box-shadow:0 0 3px rgba(0,0,0,.3);
	width: auto;
}
.foton_debug table table td {
	border: 1px solid #999 !important;
	padding: 2px 5px; 
}
.foton_debug table td {
	background:-webkit-gradient(linear, 0 top, 0 bottom, from(rgba(255,255,255,.5)), to(rgba(255,255,255,0)));
	background-image: -moz-linear-gradient(rgba(255,255,255,.5), rgba(255,255,255,0));
	padding: 1px 5px;
	border-top: 1px solid rgba(0,0,0,.1);
	vertical-align: middle;
}
.foton_debug table tr:first-child td {
	border: none;
}
.foton_debug_level_PHP {background: #FEE; color: #C00}
.foton_debug_level_DB {background: #FEF; color: #909}
.foton_debug_level_DEBUG {background-color: #DEF; color: #057}
.foton_debug_level_USER {background: #EFE; color: #090}

.foton_path {
	background:#000;
	padding:0 5px;
	color:rgba(255,255,255,1);
	text-shadow:0 1px 0 rgba(0,0,0,.3);
	-moz-border-radius:10px;
	-webkit-border-radius:10px;
	border-radius:10px;
	-moz-box-shadow:0 1px 0 #FFF;
	-webkit-box-shadow:0 1px 0 #FFF;
	box-shadow:0 1px 0 #FFF;
}
.foton_Root {background:#A55}
.foton_System {background:#A55}
.foton_Application {background:#595}
.foton_Component {background:#499}
.foton_Template {background:#57A}
.foton_Extension {background:#959}

.foton_col1 {color: #039 !important; width:1px; white-space: nowrap;}
.foton_col2 {color: #999 !important; width:1px}
.foton_col3 {color: #000 !important}

.foton_NULL {color: #C00}
.foton_boolean {color: #C60}
.foton_integer {color: #C0C}
.foton_double {color: #C0C}
</style>
<? $this->head_end() ?>

<br style="clear:both" />
<br style="clear:both" />

<div class='foton_debug'>

	<div>
		<h5>Benchmark</h5>
		<table>
			<tr>
				<td width="90">Elapsed time:</td>
				<td>{elapsed_time} ms</td>
			</tr>
			<tr>
				<td>Memory usage:</td>
				<td>{memory_usage} MB</td>
			</tr>
		</table>
	</div>
	
	<div style="background:#C99">
		<h5>System log</h5>
		<table>
			<? $last_microtime = BENCHMARK_START ?>
			<? foreach (sys::$log as $i => $log): ?>
				<? $next_microtime = isset(sys::$log[$i+1]) ? sys::$log[$i+1]['microtime'] : microtime() ?>
				<tr class="foton_debug_level_<?=_debug_level($log['level']) ?>">
					<td width="1"><?=_debug_benchmark(BENCHMARK_START, $log['microtime']) ?></td>
					<td width="1"><?=_debug_benchmark($log['microtime'], $next_microtime) ?></td>
					<td width="1"><?=_debug_level($log['level']) ?></td>
					<td width="1"><?=$log['type'] ?></td>
					<td><?=$log['message'] ?></td>
					<td><?=$log['run_time'] ?></td>
				</tr>
				<? $last_microtime = $log['microtime'] ?>
			<? endforeach ?>
		</table>
	</div>
	
	
	
	<div style="background:#79C">
		<h5>Core objects</h5>
		<? if($path = _debug_get_path()): ?>
			<table>
				<tr>
					<td>
						<b><?=$path ?></b>
						(<a href="?debud_set_path=">Close</a>)
					</td>
				</tr>
			</table>
		<? endif ?>
		<? $var = array(
			'sys::$config' => &sys::$config,
			'sys::$lib'    => &sys::$lib,
			'sys::$ext'    => &sys::$ext,
			'sys::$com'    => &sys::$com
		) ?>
		<?=_debug_var($var) ?>
	</div>
	
	
	
	<? if (count($_POST)): ?>
	<div style="background:#9C9">
		<h5>POST</h5>
		<table>
			<? $i = 1 ?>
			<? foreach ($_POST as $key => $val): ?>
			<tr>
				<td width="1"><?=$i++ ?></td>
				<td width="1"><?=$key ?></td>
				<td><?=$val ?></td>
			</tr>
			<? endforeach ?>
		</table>
	</div>
	<? endif ?>
	
	
	
	<? if (count($_COOKIE)): ?>
	<div style="background:#C97">
		<h5>Cookies</h5>
		<table>
			<? $i = 1 ?>
			<? foreach ($_COOKIE as $key => $val): ?>
			<tr>
				<td width="1"><?=$i++ ?></td>
				<td width="1"><?=$key ?></td>
				<td><?=$val ?></td>
			</tr>
			<? endforeach ?>
		</table>
	</div>
	<? endif ?>
	
	
	<? if (isset($_SESSION)): ?>
	<div style="background:#C69">
		<h5>Session values</h5>
		<table>
			<? $i = 1 ?>
			<? foreach ($_SESSION as $key => $val): ?>
			<tr>
				<td width="1"><?=$i++ ?></td>
				<td width="1"><?=$key ?></td>
				<td><?=$val ?></td>
			</tr>
			<? endforeach ?>
		</table>
	</div>
	<? endif ?>
	
	
	
	<div style="background:#C9C">
		<h5>Classes</h5>
		<table>
			<? $enable  = FALSE ?>
			<? $classes = get_declared_classes() ?>
			<? $i = 1 ?>
			<? foreach ($classes as $class): ?>
				<? if ($class == 'sys') $enable = TRUE ?>
				<? if ( ! $enable) continue ?>
				<tr>
					<td width="1"><?=$i++ ?></td>
					<td><?=$class ?></td>
				</tr>
			<? endforeach ?>
		</table>
	</div>
	
	
	
	<div>
		<h5>Required files</h5>
		<table>
			<? $include_files = get_included_files() ?>
			<? foreach ($include_files as $i => $file): ?>
			<? list($type, $file) = _debug_parse_path($file) ?>
			<tr>
				<td width="1"><?=($i+1) ?></td>
				<td width="1">
					<span class="foton_path foton_<?=$type ?>"><?=($type) ?></span>
				</td>
				<td><?=$file ?></td>
			</tr>
			<? endforeach ?>
		</table>
	</div>

	<em>FotonFramework: <?=FOTON_VERSION ?> <?=FOTON_VERSION_STATUS ?></em>
</div>

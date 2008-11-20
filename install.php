<?php
//Config writer for IMathAS
if (file_exists("config.php")) {
	echo "<html><body>config.php already exists.  Aborting.  To reinstall, delete existing config.php</body></html>";
	exit;
}
if (isset($_POST['dbserver'])) {
	
	$contents = "
<?php
//IMathAS Math Config File.  Adjust settings here!

//database access settings
\$dbserver = \"{$_POST['dbserver']}\";
\$dbname = \"{$_POST['dbname']}\";
\$dbsusername = \"{$_POST['dbusername']}\";
\$dbpassword = \"{$_POST['dbpassword']}\";

//install name
\$installname = \"{$_POST['installname']}\";

//login prompts
\$loginprompt = \"{$_POST['loginprompt']}\";
\$longloginprompt = \"{$_POST['longloginprompt']}\";
\$loginformat = '";

if ($_POST['loginformat']==0) {
	$contents .= '/^\w+$/';
} else if ($_POST['loginformat']==1) {
	$contents .= '/^\d{9}$/';
} else if ($_POST['loginformat']==2) {
	$contents .= '/^\d{3}-\d{2}-\d{4}$/';
} else if ($_POST['loginformat']==3) {
	$contents .= $_POST['loginformatother'];
}
$contents .= "';

//require email confirmation of new users?
\$emailconfirmation = {$_POST['emailconfirmation']};

//email to send notices from
\$sendfrom = \"{$_POST['sendfrom']}\";

//color shift icons as deadline approaches?
\$colorshift = {$_POST['colorshift']};

//path settings
//web path to install
\$imasroot = \"{$_POST['imasroot']}\";

//mimetex path
\$mathimgurl = \"{$_POST['mathimgurl']}\";

//enable lti?
\$enablesimplelti = {$_POST['enablesimplelti']};

//allow nongroup libs?
\$allownongrouplibs = {$_POST['allownongrouplibs']};

//allow course import of questions?
\$allowcourseimport = {$_POST['allowcourseimport']};

//allow macro install?
\$allowmacroinstall = {$_POST['allowmacroinstall']};

";

if ($_POST['mathchaturl']==0) {
	$contents .= "//Math chat disabled \n//\$mathchaturl = \"\$imasroot/mathchat/index.php\";\n\n";	
} else if ($_POST['mathchaturl']==1) {
	$contents .= "//Math chat enabled \n\$mathchaturl = \"\$imasroot/mathchat/index.php\";\n\n";	
} else if ($_POST['mathchaturl']==2) {
	$contents .= "//Math chat enabled \n\$mathchaturl = \"{$_POST['mathchaturlurl']}\";\n\n";	
}

if (!empty($_POST['sessionpath'])) {
	$contents .= "//session path \n\$sessionpath = \"{$_POST['sessionpath']}\";\n\n";
} else {
	$contents .= "//session path \n//\$sessionpath = \"\";\n\n";
}

if (!empty($_POST['AWSkey']) && !empty($_POST['AWSsecret']) && !empty($_POST['AWSbucket'])) {
	$contents .= "//Amazon S3 access for file upload \n
\$AMWkey = \"{$_POST['AWSkey']}\";\n
\$AMWsecret = \"{$_POST['AWSsecret']}\";\n
\$AMWbucket = \"{$_POST['AWSbucket']}\";\n\n";
} else {
	$contents .= "//Amazon S3 access for file upload \n
//\$AMWkey = \"{$_POST['AWSkey']}\";\n
//\$AMWsecret = \"{$_POST['AWSsecret']}\";\n
//\$AMWbucket = \"{$_POST['AWSbucket']}\";\n\n";
}
	
$contents .= '
//no need to change anything from here on
  /* Connecting, selecting database */
  if (!isset($dbsetup)) {
	 $link = mysql_connect($dbserver,$dbusername, $dbpassword) 
	  or die("<p>Could not connect : " . mysql_error() . "</p></div></body></html>");
	 mysql_select_db($dbname) 
	  or die("<p>Could not select database</p></div></body></html>");
	  
	  unset($dbserver);
	  unset($dbusername);
	  unset($dbpassword);
  }
  //clean up post and get if magic quotes aren\'t on
  function addslashes_deep($value) {
	return (is_array($value) ? array_map(\'addslashes_deep\', $value) : addslashes($value));
  }
  if (!get_magic_quotes_gpc()) {
   $_GET    = array_map(\'addslashes_deep\', $_GET);
   $_POST  = array_map(\'addslashes_deep\', $_POST);
   $_COOKIE = array_map(\'addslashes_deep\', $_COOKIE);
  } 
?>';
$file = fopen('config.php','w');
$f =  fwrite($file,$contents);
fclose($file);
?>
<html>
<body>
<?php
if ($f!==false) {
	echo "config.php written.<br/>";
} else {
	echo "error writing config!  Copy config.php.dist to config.php and edit manually.<br/>";
}

$c1 = chmod('assessment/libs',0755);
$c2 =chmod('assessment/qimages',0755);
$c3 =chmod('admin/import',0755);
$c4 =chmod('course/files',0755);
$c5 =chmod('filter/graph/imgs',0755);

if ($c1 && $c2 && $c3 && $c4 && $c5) {
	echo 'Permissions set on writeable directories<br/>';
} else {
	echo 'Error setting directory permissions.  See readme.html for a list of directories that need to be writable by the web server.<br/>';
}

$c6 =copy('infoheader.php.dist','infoheader.php');
$c7 =copy('loginpage.php.dist','loginpage.php');
$c8 =copy('newinstructor.php.dist','newinstructor.php');

if ($c6 && $c7 && $c8) {
	echo 'Local copies of infoheader.php, loginpage,php, and newinstructor.php have been created. You may want to personalize these.<br/>';
} else {
	echo 'Couldn\'t make copies of infoheader.php, loginpage,php, and newinstructor.php.  Please copy the .dist files as described in readme.html<br/>';
}
?>
<form method="post" action="dbsetup.php">
<input type="hidden" name="dbsetupmethod" value="<?php echo $_POST['dbcreate'];?>" />
<?php 
if ($_POST['dbcreate']==2) {
?>
<input type="hidden" name="dbauthuser" value="<?php echo $_POST['dbauthuser'];?>" />
<input type="hidden" name="dbauthpass" value="<?php echo $_POST['dbauthpass'];?>" />
<?php
} 
?>
<input type="submit" value="Continue to creating database"/>
</form>
</body>
</html>
<?php
	
} else {
?>
<html>
<head>
<link rel="stylesheet" href="imascore.css" type="text/css" />
<style type="text/css">
p {
	background: #ccf;
	padding: 10px;
	margin: 5px;
}
p.imp {
	background: #fcc;	
}
</style>
<title>IMathAS Install</title>
</head>
<body>
<h2>IMathAS Install</h2>
<p>This page will help you configure IMathAS.  The database settings <b>must</b> be
changed.  The rest of the settings autopopulate to reasonable defaults but can be
changed to allow for customization.</p>
<form method="post" action="configwriter.php?submit=true">
<h3>Database Settings</h3>
<p class="imp">
Database server.  Could be localhost or mysql.yoursite.edu<br/>
<input type="text" name="dbserver" value="localhost" />
</p>

<p class="imp">
Database name.<br/>
<input type="text" name="dbname" value="imathasdb" />
</p>

<p class="imp">
Database username.  The mysql user who has privileges to this database.<br/>
<input type="text" name="dbusername" value="imasuser" />
</p>

<p class="imp">
Database password.  The password for the database user above.<br/>
<input type="text" name="dbpassword" value="" />
</p>

<p class="imp">How do you want the database created?<br/>
<input type="radio" name="dbcreate" checked="checked" value="0"/> My hosting company requires that I create databases
   and database users through their control panel, so I've already created the database and database user<br/>
<input type="radio" name="dbcreate" value="1"/> The database user I provided above already exists in the database and has 
   database creation privileges, so use that userid to create the database.<br/>
<input type="radio" name="dbcreate" value="2"/> The database and database user are not yet created.  Create them using this
   username and password that has database and user creation privileges.  Username: <input type="text" name="dbauthuser"/> Password: 
   <input type="password" name="dbauthpass" />
</p>

<h3>Customization</h3>
<p>
The name of this installation.  This allows you to custom-brand the site<br/>
<input type="text" name="installname" value="IMathAS" />
</p>

<p>
A small logo or name to display on the upper right of course pages.  Can use &lt;img src="/path/to/img.gif"/&gt;
for an image, or just put in some text or your installation name.  Image should be 120x80px if used.<br/>
<input type="text" name="smallheaderlogo" value="IMathAS" />
</p>

<p>
Prompt for login.  Could be username, Student ID, email address, etc.<br/>
<input type="text" name="loginprompt" value="Username" />
</p>

<p>
Prompt for new users to create a username<br/>
<input type="text" size="80" name="longloginprompt" value="Enter a username.  Use only numbers, letters, or the _ character." />
</p>

<p>
Login format.<br/>
<input type="radio" name="loginformat" value="0" checked="checked" />Letters, numbers, and _ are only allowed characters<br/>
<input type="radio" name="loginformat" value="1" />Require a 9-digit number<br/>
<input type="radio" name="loginformat" value="2" />SSN type format: 555-55-5555<br/>
<input type="radio" name="loginformat" value="3" />Other.  Provide a regular expression: <input type="loginformatother" value="/\d{7}/" />
</p>

<p>
Require new users to respond to an email to verify their account before allowing 
them to log in?<br/>
<input type="radio" name="emailconfirmation" value="true"/>Yes<br/>
<input type="radio" name="emailconfirmation" value="false" checked="checked" />No
</p>

<p>
Email address to send confirmation emails, new message notifications and new
forum post notifications from.<br/>
<input type="text" name="sendfrom" size="80" value="imathas@yoursite.edu" />
</p>

<p>
Color shift icons from green to red as deadlines approach?<br/>
<input type="radio" name="colorshift" value="true" checked="checked" /> Yes<br/>
<input type="radio" name="colorshift" value="false" /> No
</p>

<h3>Settings</h3>

<p>
Path to IMathAS install.  Blank if install is in web root directory.  Might be something like "/imathas" if in a
subdirectory.<br/>
<input type="text" name="imasroot" value="<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); ?>" />
</p>

<p>
Absolute path or full url to Mimetex CGI, for math image fallback.  If you cannot install it 
on your local installation, you can use the default public server.<br/>
<input type="text" name="mathimgurl" size="80" value="http://www.imathas.com/cgi-bin/mimetex.cgi" />
</p>

<p>
Enable live chat?  If lots of people use this, it could overburden the server.  You can
use a live chat from another server to distribute load if you want.<br/>
<input type="radio" name="mathchaturl" value="0" checked="checked" /> Disable<br/>
<input type="radio" name="mathchaturl" value="1" /> Enable using this server<br/>
<input type="radio" name="mathchaturl" value="2" /> Enable, using this url:
<input type="text" name="mathchaturlurl" size="80" value="http://otherserver.edu/imathasroot/mathchat/index.php"/>
</p>

<p>
Enable use of IMathAS as a SimpleLTI producer?<br/>
<input type="radio" name="enablesimplelti" value="true" checked="checked" /> Yes<br/>
<input type="radio" name="enablesimplelti" value="false" /> No
</p>

<p>
Should non-admins be allowed to create open-to-all libraries? On a single-school 
install, set to yes; for larger installs that plan to use the Instructor Groups features, 
set to no<br/>
<input type="radio" name="allownongrouplibs" value="true"  /> Yes<br/>
<input type="radio" name="allownongrouplibs" value="false" checked="checked" /> No
</p>

<p>
Should anyone be allowed to import/export questions and libraries from the 
course page?  Intended for easy sharing between systems, but the course page
is cleaner if turned off.<br/>
<input type="radio" name="allowcourseimport" value="true"  /> Yes<br/>
<input type="radio" name="allowcourseimport" value="false" checked="checked" /> No
</p>

<p>
Allow installation of macro files by admins?  Macro files contain a large
security risk.  If you are going to have many admins, and don't trust the
security of their passwords, you should set this to false.  Installing
macros is equivalent in security risk to having FTP access to the IMathAS
server.<br/>
For single-admin systems, it is recommended you leave this as false, and
change it when you need to install a macro file.<br/>
<input type="radio" name="allowmacroinstall" value="true" checked="checked" /> Yes<br/>
<input type="radio" name="allowmacroinstall" value="false" /> No
</p>


<p>
Set PHP session path away from default.  Leave blank to use default, or provide
an absolute file system path. 
Changing is usually not necessary unless your site is on a server farm, or
you're on a shared server and want more security of session data.
Make sure this directory has write access by the server process.
<br/>
<input type="text" name="sessionpath" size="80" value="" />
</p>


<p>
For text editor file/image uploads and assessment file uploads, you can use
Amazon S3 service to hold these files.  If using this option, provide your
Amazon S3 key and secret below.  You'll also need to create a bucket and
specify it below.  Leave these blank to disable file uploads.<br/>
Amazon S3 Key:<input type="text" name="AWSkey" value=""/><br/>
Amazon S3 Secret:<input type="password" name="AWSsecret" value=""/><br/>
Amazon S3 Bucket:<input type="text" name="AWSbucket" value=""/><br/>
</p>

<input type="submit" value="Create Config, set up Database" />
<input type="reset" value="Reset Defaults"/>
</form>

<?php	
}
?>
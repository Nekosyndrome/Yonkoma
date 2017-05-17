<!--&THEMENAME-->futaba Theme<!--/&THEMENAME-->
<!--&THEMEVER-->v20140603<!--/&THEMEVER-->
<!--&THEMEAUTHOR-->Pixmicat! Development Team<!--/&THEMEAUTHOR-->
<!--&HEADER--><!DOCTYPE html>
<html lang="zh-TW">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>{$TITLE}</title>
<link rel="stylesheet" type="text/css" href="css/mainstyle.css" />
<!--/&HEADER-->

<!--&JSHEADER-->
<script type="text/javascript">
// <![CDATA[
var msgs=['{$JS_REGIST_WITHOUTCOMMENT}','{$JS_REGIST_UPLOAD_NOTSUPPORT}','{$JS_CONVERT_SAKURA}'];
var ext="{$ALLOW_UPLOAD_EXT}".toUpperCase().split("|");
// ]]>
</script>
<script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="js/jquery.draggable.js"></script>
<script type="text/javascript" src="js/script.js"></script>
<!--/&JSHEADER-->

<!--&TOPLINKS-->
<div id="toplink">
{$HOME} {$SEARCH} {$HOOKLINKS} {$TOP_LINKS} {$STATUS} {$ADMIN} {$REFRESH}
</div>
<!--/&TOPLINKS-->

<!--&BODYHEAD-->
<body>

<div id="header">
<!--&TOPLINKS/-->
<br />
<h1>{$TITLE}</h1>
<hr class="top" />
</div>
<!--/&BODYHEAD-->

<!--&POSTFORM-->
<div id="postform">
<!--&IF($FORMTOP,'{$FORMTOP}','')-->

<form action="{$SELF}" method="post" enctype="multipart/form-data" id="postform_main">
<input type="hidden" name="mode" value="{$MODE}" />
<input type="hidden" name="MAX_FILE_SIZE" value="{$MAX_FILE_SIZE}" />
<input type="hidden" name="upfile_path" value="" />

<!--&IF($RESTO,'{$RESTO}','')-->
<div style="text-align: center;">
<table id="postform_tbl" style="padding: 10px;border-spacing: 10px; margin: 0px auto; text-align: left;">
<tr><td class="Form_bg"><b>{$FORM_NAME_TEXT}</b></td><td>{$FORM_NAME_FIELD} {$FORM_SUBMIT}</td></tr>
<!--&IF($MAIN_PAGE,'<tr><td class="Form_bg"><b>{$FORM_TOPIC_TEXT}</b></td><td>{$FORM_TOPIC_FIELD}</td></tr>','')-->
<tr><td class="Form_bg"><b>{$FORM_COMMENT_TEXT}</b></td><td>{$FORM_COMMENT_FIELD}</td></tr>
<!--&IF($FORM_ATTECHMENT_FIELD,'<tr><td class="Form_bg"><b>{$FORM_ATTECHMENT_TEXT}</b></td><td>{$FORM_ATTECHMENT_FIELD}[{$FORM_NOATTECHMENT_FIELD}<label for="noimg">{$FORM_NOATTECHMENT_TEXT}</label>]','')-->
<!--&IF($FORM_CONTPOST_FIELD,'[{$FORM_CONTPOST_FIELD}<label for="up_series">{$FORM_CONTPOST_TEXT}</label>]','')-->
 [{$FORM_EMAIL_FIELD}<label for="femail">SAGE</label>]
<!--&IF($FORM_ATTECHMENT_FIELD,'</td></tr>','')-->
<!--&IF($FORM_CATEGORY_FIELD,'<tr><td class="Form_bg"><b>{$FORM_CATEGORY_TEXT}</b></td><td>{$FORM_CATEGORY_FIELD}<small>{$FORM_CATEGORY_NOTICE}</small></td></tr>','')-->
<!--&IF($NONE,'<tr><td class="Form_bg"><b>{$FORM_DELETE_PASSWORD_TEXT}</b></td><td>{$FORM_DELETE_PASSWORD_FIELD}<small>{$FORM_DELETE_PASSWORD_NOTICE}</small></td></tr>','')-->
{$FORM_EXTRA_COLUMN}
<tr><td colspan="2">
<div id="postinfo">
<ul>{$FORM_NOTICE}
<!--&IF($FORM_NOTICE_STORAGE_LIMIT,'{$FORM_NOTICE_STORAGE_LIMIT}','')-->
{$HOOKPOSTINFO}
{$ADDITION_INFO}
</ul>
</div>
</td></tr>
</table>
</div>
<hr />

</form>
<!--&IF($FORMBOTTOM,'{$FORMBOTTOM}','')-->
</div>
<!--/&POSTFORM-->
<!--&FOOTER-->
<div id="footer">
{$FOOTER}
</div>

</body>
</html>
<!--/&FOOTER-->

<!--&ERROR-->
<div id="error">
<div style="text-align: center; font-size: 1.5em; font-weight: bold;">
<span style="color: red;">{$MESG}</span><p />
<a href="{$SELF2}">{$RETURN_TEXT}</a>　<a href="javascript:history.back();">{$BACK_TEXT}</a>
</div>
<hr />
</div>
<!--/&ERROR-->


<!--&THREAD-->
<div class="post threadpost" data-no="{$NO}" id="r{$NO}">
<!--&IF($IMG_BAR,'<div class="file-text">{$IMG_BAR}</div>','')-->
{$IMG_SRC}
<div class="post-head"><input type="checkbox" name="{$NO}" value="delete" /><span class="title">{$SUB}</span><span class="name">{$NAME}</span><span class="now">{$NOW}</span><span class="qlink" data-no="{$NO}">{$QUOTEBTN}</span><span class="rlink">{$REPLYBTN}</span></div>
<div class="quote">{$COM}</div>
<!--&IF($CATEGORY,'<div class="category">{$CATEGORY_TEXT}{$CATEGORY}</div>','')-->
{$WARN_OLD}{$WARN_BEKILL}{$WARN_ENDREPLY}{$WARN_HIDEPOST}</div>
<!--/&THREAD-->

<!--&REPLY-->
<div class="post reply" data-no="{$NO}" id="r{$NO}">
<div class="post-head"><input type="checkbox" name="{$NO}" value="delete" /><span class="name">{$NAME}</span><span class="now">{$NOW}</span><span class="qlink" data-no="{$NO}">{$QUOTEBTN}</span></div>
<!--&IF($IMG_BAR,'<div class="file-text">{$IMG_BAR}</div>','')-->
{$IMG_SRC}
<div class="quote">{$COM}</div>
<!--&IF($CATEGORY,'<div class="category">{$CATEGORY_TEXT}{$CATEGORY}</div>','')-->
{$WARN_BEKILL}</div>
<!--/&REPLY-->

<!--&SEARCHRESULT-->
<div class="threadpost">
<span class="title">{$SUB}</span>
{$NAME_TEXT}<span class="name">{$NAME}</span> [{$NOW}] No.{$NO}
<div class="quote">{$COM}</div>
<!--&IF($CATEGORY,'<div class="category">{$CATEGORY_TEXT}{$CATEGORY}</div>','')-->
</div>
<!--&REALSEPARATE/-->
<!--/&SEARCHRESULT-->

<!--&THREADSEPARATE-->
<hr />
<!--/&THREADSEPARATE-->

<!--&REALSEPARATE-->
<hr />
<!--/&REALSEPARATE-->

<!--&DELFORM-->
<div id="del">
<table style="float: right;">
<tr><td style="text-align:center;white-space: nowrap;">
{$DEL_HEAD_TEXT}[{$DEL_IMG_ONLY_FIELD}<label for="onlyimgdel">{$DEL_IMG_ONLY_TEXT}</label>]<br />
{$DEL_PASS_TEXT}{$DEL_PASS_FIELD}{$DEL_SUBMIT_BTN}
</td></tr>
</table>
</div>
<!--/&DELFORM-->

<!--&MAIN-->
<div id="contents">
{$THREADFRONT}
<form action="{$SELF}" method="post">
<div id="threads" class="autopagerize_page_element">
{$THREADS}
</div>
{$THREADREAR}
<!--&DELFORM/-->
</form>
{$PAGENAV}
</div>
<!--/&MAIN-->

<?php
include_once "common.skin.php";

header('Content-Type: text/xsl');

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<xsl:stylesheet  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template match="/">
<?php    
$begin_time = get_microtime();

// 현재 접속자
// 게시판 제목에 ' 포함되면 오류 발생
$g5['lo_location'] = addslashes($g5['title']);
if (!$g5['lo_location'])
    $g5['lo_location'] = addslashes($_SERVER['REQUEST_URI']);
$g5['lo_url'] = addslashes($_SERVER['REQUEST_URI']);
if (strstr($g5['lo_url'], '/'.G5_ADMIN_DIR.'/') || $is_admin == 'super') $g5['lo_url'] = '';

/*
// 만료된 페이지로 사용하시는 경우
header("Cache-Control: no-cache"); // HTTP/1.1
header("Expires: 0"); // rfc2616 - Section 14.21
header("Pragma: no-cache"); // HTTP/1.0
*/
?>
<html lang="ko">
<head>
<meta charset="utf-8" />
<?php
if (G5_IS_MOBILE) {
    echo '<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes" />'.PHP_EOL;
    echo '<meta name="HandheldFriendly" content="true" />'.PHP_EOL;
    echo '<meta name="format-detection" content="telephone=no" />'.PHP_EOL;
} else {
    echo '<meta http-equiv="imagetoolbar" content="no" />'.PHP_EOL;
    echo '<meta http-equiv="X-UA-Compatible" content="IE=10,chrome=1" />'.PHP_EOL;
}

if($config['cf_add_meta'])
    echo preg_replace("/[^<>]+/", "$0 /", $config['cf_add_meta']).PHP_EOL;
?>
<title><xsl:value-of select="board_list/title" /></title>
<?php
if (defined('G5_IS_ADMIN')) {
    echo '<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css" />'.PHP_EOL;
} else {
    echo '<link rel="stylesheet" href="'.G5_CSS_URL.'/'.(G5_IS_MOBILE?'mobile':'default').'.css" />'.PHP_EOL;
}
?>
<!--[if lte IE 8]>
<script src="<?php echo G5_JS_URL ?>/html5.js"></script>
<![endif]-->
<script>
// 자바스크립트에서 사용하는 전역변수 선언
var g5_url       = "<?php echo G5_URL ?>";
var g5_bbs_url   = "<?php echo G5_BBS_URL ?>";
var g5_is_member = "<?php echo isset($is_member)?$is_member:''; ?>";
var g5_is_admin  = "<?php echo isset($is_admin)?$is_admin:''; ?>";
var g5_is_mobile = "<?php echo G5_IS_MOBILE ?>";
var g5_bo_table  = "<?php echo isset($bo_table)?$bo_table:''; ?>";
var g5_sca       = "<?php echo isset($sca)?$sca:''; ?>";
var g5_editor    = "<?php echo ($config['cf_editor'] && $board['bo_use_dhtml_editor'])?$config['cf_editor']:''; ?>";
var g5_cookie_domain = "<?php echo G5_COOKIE_DOMAIN ?>";
<?php
if ($is_admin) {
    echo 'var g5_admin_url = "'.G5_ADMIN_URL.'";'.PHP_EOL;
}
?>
</script>
<script src="<?php echo G5_JS_URL ?>/jquery-1.8.3.min.js"></script>
<script src="<?php echo G5_JS_URL ?>/jquery.menu.js"></script>
<script src="<?php echo G5_JS_URL ?>/common.js"></script>
<script src="<?php echo G5_JS_URL ?>/wrest.js"></script>
<?php
if(G5_IS_MOBILE) {
    echo '<script src="'.G5_JS_URL.'/modernizr.custom.70111.js"></script>'.PHP_EOL; // overflow scroll 감지
}
if(!defined('G5_IS_ADMIN'))
    echo $config['cf_add_script'];
?>
</head>
<body>
<?php
if ($is_member) { // 회원이라면 로그인 중이라는 메세지를 출력해준다.
    $sr_admin_msg = '';
    if ($is_admin == 'super') $sr_admin_msg = "최고관리자 ";
    else if ($is_admin == 'group') $sr_admin_msg = "그룹관리자 ";
    else if ($is_admin == 'board') $sr_admin_msg = "게시판관리자 ";

    echo '<div id="hd_login_msg">'.$sr_admin_msg.$member['mb_nick'].'님 로그인 중 ';
    echo '<a href="'.G5_BBS_URL.'/logout.php">로그아웃</a></div>';
}
?>    
<!-- head.sub.php 끝 -->



<script type="text/javascript">
$(document).ready(function(){
    $("#sfl").val("<xsl:value-of select="board_list/search/sfl" />");
});
</script>



<!-- 내용 -->
<?php
// 선택옵션으로 인해 셀합치기가 가변적으로 변함
$colspan = 5;

if ($is_checkbox) $colspan++;
if ($is_good) $colspan++;
if ($is_nogood) $colspan++;

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
?>
<link rel="stylesheet" href="<?=$board_skin_url?>/style.css" />

<h2 id="container_title"><?php echo $board['bo_subject'] ?><span class="sound_only"> 목록</span></h2>

<!-- 게시판 목록 시작 { -->
<div id="bo_list" style="width:<?php echo $width; ?>">

    <!-- 게시판 카테고리 시작 { -->
    <xsl:if test="board_list/use_category = 'true'">
        <nav id="bo_cate">
            <h2><xsl:value-of select="board_list/bo_subject"/> 카테고리</h2>
            <ul id="bo_cate_ul">
                <xsl:value-of select="board_list/category_option" />
            </ul>
        </nav>
    </xsl:if>
    <!-- } 게시판 카테고리 끝 -->

    <!-- 게시판 페이지 정보 및 버튼 시작 { -->
    <div class="bo_fx">
        <div id="bo_list_total">
            <span>Total <xsl:value-of select="board_list/total_count" />건</span>
            <xsl:value-of select="board_list/page" /> 페이지
        </div>

        <xsl:if test="board_list/use_rss = 'true' or board_list/perm_admin = 'true' or board_list/writable = 'true'">
        <ul class="btn_bo_user">
            <xsl:if test="board_list/use_rss = 'true'">
                <li><a href="./rss.php?bo_table=<?=$bo_table?>" class="btn_b01">RSS</a></li>
            </xsl:if>
            <xsl:if test="board_list/perm_admin = 'true'">
                <li><a href="<?=$g5['path']?>/adm/board_form.php?w=u&amp;bo_table=<?=$bo_table?>" class="btn_admin">관리자</a></li>
            </xsl:if>
            <xsl:if test="board_list/writable = 'true'">
                <li><a href="./write.php?bo_table=<?=$bo_table?>" class="btn_b02">글쓰기</a></li>
            </xsl:if>
        </ul>
        </xsl:if>
    </div>
    <!-- } 게시판 페이지 정보 및 버튼 끝 -->

    <form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="{board_list/bo_table}" />
    <input type="hidden" name="sfl" value="{board_list/search/sfl}" />
    <input type="hidden" name="stx" value="{board_list/search/stx}" />
    <input type="hidden" name="spt" value="{board_list/search/spt}" />
    <input type="hidden" name="sca" value="{board_list/search/sca}" />
    <input type="hidden" name="sst" value="{board_list/search/sst}" />
    <input type="hidden" name="sod" value="{board_list/search/sod}" />
    <input type="hidden" name="page" value="{board_list/page}" />
    <input type="hidden" name="sw" value="" />

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption><xsl:value-of select="board_list/bo_subject" /> 목록</caption>
        <thead>
        <tr>
            <th scope="col">번호</th>
            <xsl:if test="board_list/use_checkbox = 'true'">
            <th scope="col">
                <label for="chkall" class="sound_only">현재 페이지 게시물 전체</label>
                <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);" />
            </th>
            </xsl:if>
            <th scope="col">제목</th>
            <th scope="col">글쓴이</th>
            <th scope="col">날짜</th>
            <th scope="col">조회</th>
            <xsl:if test="board_list/use_good = 'true'">
                <th scope="col">추천</th>
            </xsl:if>
            <xsl:if test="board_list/use_nogood = 'true'">
                <th scope="col">비추천</th>
            </xsl:if>
        </tr>
        </thead>
        <tbody>
        <xsl:choose>
        <xsl:when test="board_list/list/item">
            <xsl:for-each select="board_list/list/item">
                <xsl:element name="tr">
                    <xsl:attribute name="class"><xsl:if test="is_notice = 'true'">bo_notice</xsl:if></xsl:attribute>
                    <td class="td_num">
                    <xsl:choose>
                    <xsl:when test="is_notice = 'true'"><!-- 공지사항 -->
                        <strong>공지</strong>
                    </xsl:when>
                    <xsl:when test="wr_id = /board_list/wr_id">
                        <span class="bo_current">열람중</span>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="num" />
                    </xsl:otherwise>
                    </xsl:choose>
                    </td>
                    <xsl:if test="/board_list/use_checkbox = 'true'">
                    <td class="td_chk">
                        <label for="chk_wr_id_{position()}" class="sound_only"><xsl:value-of select="subject" /></label>
                        <input type="checkbox" name="chk_wr_id[]" value="{wr_id}" id="chk_wr_id_{position()}" />
                    </td>
                    </xsl:if>
                    <td class="td_subject">
                        <xsl:value-of select="icon_reply" />
                        <xsl:if test="/board_list/use_category = 'true' and ca_name">
                            <a href="{ca_name_href}" class="bo_cate_link"><xsl:value-of select="ca_name" /></a>
                        </xsl:if>
                        <a href="{href}">
                            <xsl:value-of select="subject" />&#160;
<xsl:if test="comment_cnt > 0">
                                <span class="sound_only">댓글</span>
                                <xsl:value-of select="comment_cnt" />&#160;
                                <span class="sound_only">개</span>
                            </xsl:if>
                        </a>
                        <xsl:copy-of select="icon_new" />
                        <xsl:copy-of select="icon_hot" />
                        <xsl:copy-of select="icon_file" />
                        <xsl:copy-of select="icon_link" />
                        <xsl:copy-of select="icon_secret" />
                    </td>
                    <td class="td_name sv_use">
                        <xsl:copy-of select="name" />
                    </td>
                    <td class="td_date">
                        <xsl:value-of select="datetime2" />
                    </td>
                    <td class="td_num">
                        <xsl:value-of select="wr_hit" />
                    </td>
                    <xsl:if test="/board_list/use_good = 'true'">
                        <td>
                            <xsl:value-of select="wr_good" />
                        </td>
                    </xsl:if>
                    <xsl:if test="/board_list/use_nogood = 'true'">
                        <td>
                            <xsl:value-of select="wr_nogood" />
                        </td>
                    </xsl:if>
                </xsl:element>
            </xsl:for-each>
        </xsl:when>
        <xsl:otherwise>
            <tr>
                <td colspan="<?=$colspan?>" class="empty_table">
                    게시물이 없습니다.
                </td>
            </tr>
        </xsl:otherwise>
        </xsl:choose>
        </tbody>
        </table>
    </div>

    <xsl:if test="board_list/use_checkbox = 'true' or board_list/writable = 'true'">
        <div class="bo_fx">
            <xsl:if test="board_list/use_checkbox = 'true'">
                <ul class="btn_bo_adm">
                    <li><input type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value" /></li>
                    <li><input type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value" /></li>
                    <li><input type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value" /></li>
                </ul>
            </xsl:if>
            <xsl:if test="board_list/writable = 'true'">
                <ul class="btn_bo_user">
                    <li><a href="./write.php?bo_table=<?=$bo_table?>" class="btn_b02">글쓰기</a></li>
                </ul>
            </xsl:if>
        </div>
    </xsl:if>
    </form>
</div>

<xsl:if test="board_list/use_checkbox = 'true'">
    <noscript>
        <p>자바스크립트를 사용하지 않는 경우<br/>별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
    </noscript>
</xsl:if>

<!-- 페이지 -->
<xsl:value-of select="board_list/write_pages" />

<!-- 게시판 검색 시작 { -->
<fieldset id="bo_sch">
    <legend>게시물 검색</legend>

    <form name="fsearch" method="get">
    <input type='hidden' name='bo_table' value='{board_list/bo_table}' />
    <input type="hidden" name="sca" value="{board_list/search/sca}" />
    <input type="hidden" name="sop" value="and" />
    <label for="sfl" class="sound_only">검색대상</label>
    <select name="sfl" id="sfl">
        <option value="wr_subject">제목</option>
        <option value="wr_content">내용</option>
        <option value="wr_subject||wr_content">제목+내용</option>
        <option value="mb_id,1">회원아이디</option>
        <option value="mb_id,0">회원아이디(코)</option>
        <option value="wr_name,1">글쓴이</option>
        <option value="wr_name,0">글쓴이(코)</option>
    </select>
    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="{board_list/search/stx}" required="true" id="stx" class="frm_input required" size="15" maxlength="20"/>
    <input type="submit" value="검색" class="btn_submit" />
    </form>
</fieldset>
<!-- } 게시판 검색 끝 -->

<xsl:if test="board_list/use_checkbox = 'true'">
    <script><![CDATA[
    function all_checked(sw) {
        var f = document.fboardlist;

        for (var i=0; i<f.length; i++) {
            if (f.elements[i].name == "chk_wr_id[]")
                f.elements[i].checked = sw;
        }
    }

    function fboardlist_submit(f) {
        var chk_count = 0;

        for (var i=0; i<f.length; i++) {
            if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
                chk_count++;
        }

        if (!chk_count) {
            alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
            return false;
        }

        if(document.pressed == "선택복사") {
            select_copy("copy");
            return;
        }

        if(document.pressed == "선택이동") {
            select_copy("move");
            return;
        }

        if(document.pressed == "선택삭제") {
            if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
                return false;

            f.removeAttribute("target");
            f.action = "./board_list_update.php";
        }

        return true;
    }

    // 선택한 게시물 복사 및 이동
    function select_copy(sw) {
        var f = document.fboardlist;

        if (sw == "copy")
            str = "복사";
        else
            str = "이동";

        var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

        f.sw.value = sw;
        f.target = "move";
        f.action = "./move.php";
        f.submit();
    }
    ]]>
    </script>
</xsl:if>
<!-- } 게시판 목록 끝 -->



<!-- tail.sub.php 시작 -->
<?php if ($is_admin == 'super') {  ?><!-- <div style='float:left; text-align:center;'>RUN TIME : <?php echo get_microtime()-$begin_time; ?><br></div> --><?php }  ?>

<!-- ie6,7에서 사이드뷰가 게시판 목록에서 아래 사이드뷰에 가려지는 현상 수정 -->
<!--[if lte IE 7]>
<script>
$(function() {
    var $sv_use = $(".sv_use");
    var count = $sv_use.length;

    $sv_use.each(function() {
        $(this).css("z-index", count);
        $(this).css("position", "relative");
        count = count - 1;
    });
});
</script>
<![endif]-->

</body>
</html>


    </xsl:template>
</xsl:stylesheet>

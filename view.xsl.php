<?php
include_once "common.skin.php";
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

header('Content-Type: text/xsl');

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<xsl:stylesheet  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:import href="<?=$board_skin_url?>/view_comment.xsl.php?bo_table=<?=$bo_table?>" />
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
<title><xsl:value-of select="board_view/wr_subject" /> - <xsl:value-of select="board_view/board_info/title" /></title>
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



<!-- 내용 -->
<?php
// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
?>
<link rel="stylesheet" href="<?=$board_skin_url?>/style.css" />

<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>

<!-- 게시물 읽기 시작 { -->
<div id="bo_v_table"><xsl:value-of select="board_view/board_info/bo_subject" /></div>

<article id="bo_v" style="width:<?php echo $width; ?>">
    <header>
        <h1 id="bo_v_title">
            <xsl:if test="board_view/ca_name">
                <xsl:value-of select="board_view/ca_name" />
            </xsl:if>
            <xsl:value-of select="board_view/wr_subject" />
        </h1>
    </header>

    <section id="bo_v_info">
        <h2>페이지 정보</h2>
        작성자 
        <strong>
            <xsl:copy-of select="board_view/name" />
            <xsl:if test="board_view/board_info/use_ip_view = 'true'">
                &#160;(<xsl:value-of select="board_view/ip" />)
            </xsl:if>
        </strong>
        <span class="sound_only">작성일</span><strong><xsl:value-of select="board_view/wr_datetime" /></strong>
        조회<strong><xsl:value-of select="board_view/wr_hit" />회</strong>
        댓글<strong><xsl:value-of select="board_view/wr_comment" />건</strong>
    </section>

    <xsl:if test="board_view/files/file">
    <!-- 첨부파일 시작 { -->
    <section id="bo_v_file">
        <h2>첨부파일</h2>
        <ul>
        <xsl:for-each select="board_view/files/file">
            <xsl:if test="not(view/*)">
                <li>
                    <a href="{href}" class="view_file_download">
                        <img src="<?php echo $board_skin_url ?>/img/icon_file.gif" alt="첨부" />
                        <strong><xsl:value-of select="source" /></strong>
                        <xsl:value-of select="content" /> (<xsl:value-of select="size" />)
                    </a>
                    <span class="bo_v_file_cnt"><xsl:value-of select="download" />회 다운로드</span>
                    <span>DATE : <xsl:value-of select="datetime" /></span>
                </li>
            </xsl:if>
        </xsl:for-each>
        </ul>
    </section>
    <!-- } 첨부파일 끝 -->
    </xsl:if>

    <xsl:if test="board_view/links/link">
     <!-- 관련링크 시작 { -->
    <section id="bo_v_link">
        <h2>관련링크</h2>
        <ul>
        <xsl:for-each select="board_view/links/link">
            <li>
                <a href="{link_href}" target="_blank">
                    <img src="<?php echo $board_skin_url ?>/img/icon_link.gif" alt="관련링크" />
                    <strong><xsl:value-of select="name" /></strong>
                </a>
                <span class="bo_v_file_cnt"><xsl:value-of select="hit" />회 연결</span>
            </li>
        </xsl:for-each>
        </ul>
    </section>
    <!-- } 관련링크 끝 -->
    </xsl:if>

    <!-- 게시물 상단 버튼 시작 { -->
    <div id="bo_v_top">
        <?php
        ob_start();
         ?>
        <xsl:if test="board_view/hrefs/prev_href or board_view/hrefs/next_href">
        <ul class="bo_v_nb">
            <xsl:if test="board_view/hrefs/prev_href != ''">
                <li><a href="{board_view/hrefs/prev_href}" class="btn_b01">이전글</a></li>
            </xsl:if>
            <xsl:if test="board_view/hrefs/next_href != ''">
                <li><a href="{board_view/hrefs/next_href}" class="btn_b01">다음글</a></li>
            </xsl:if>
        </ul>
        </xsl:if>

        <ul class="bo_v_com">
            <xsl:if test="board_view/hrefs/update_href != ''">
                <li><a href="{board_view/hrefs/update_href}" class="btn_b01">수정</a></li>
            </xsl:if>
            <xsl:if test="board_view/hrefs/delete_href != ''">
                <li><a href="{board_view/hrefs/delete_href}" class="btn_b01" onclick="del(this.href); return false;">삭제</a></li>
            </xsl:if>
            <xsl:if test="board_view/hrefs/copy_href != ''">
                <li><a href="{board_view/hrefs/copy_href}" class="btn_admin" onclick="board_move(this.href); return false;">복사</a></li>
            </xsl:if>
            <xsl:if test="board_view/hrefs/move_href != ''">
                <li><a href="{board_view/hrefs/move_href}" class="btn_admin" onclick="board_move(this.href); return false;">이동</a></li>
            </xsl:if>
            <xsl:if test="board_view/hrefs/search_href != ''">
                <li><a href="{board_view/hrefs/search_href}" class="btn_b01">검색</a></li>
            </xsl:if>
            <li><a href="{board_view/hrefs/list_href}" class="btn_b01">목록</a></li>
            <xsl:if test="board_view/hrefs/reply_href != ''">
                <li><a href="{board_view/hrefs/reply_href}" class="btn_b01">답변</a></li>
            </xsl:if>
            <xsl:if test="board_view/hrefs/write_href != ''">
                <li><a href="{board_view/hrefs/write_href}" class="btn_b02">글쓰기</a></li>
            </xsl:if>
        </ul>
        <?php
        $link_buttons = ob_get_contents();
        ob_end_flush();
         ?>
    </div>
    <!-- } 게시물 상단 버튼 끝 -->

    <section id="bo_v_atc">
        <h2 id="bo_v_atc_title">본문</h2>

        <!-- 파일 출력 -->
        <div id="bo_v_img">
            <xsl:if test="board_view/files/file">
                <xsl:for-each select="board_view/files/file">
                    <xsl:if test="view">
                        <xsl:copy-of select="view" />
                    </xsl:if>
                </xsl:for-each>
            </xsl:if>
        </div>

        <!-- 본문 내용 시작 { -->
        <div id="bo_v_con"><xsl:copy-of select="board_view/contents" /></div>
        <!-- } 본문 내용 끝 -->

        <xsl:if test="board_view/board_info/use_signature = 'true'">
            <xsl:value-of select="board_view/signature" />
        </xsl:if>

        <!-- 스크랩 추천 비추천 시작 { -->
        
        <xsl:choose>
        <xsl:when test="board_view/hrefs/scrap_href or board_view/hrefs/good_href or board_view/hrefs/nogood_href">
            <div id="bo_v_act">
                <xsl:if test="board_view/hrefs/scrap_href != ''">
                    <a href="{board_view/hrefs/scrap_href}" target="_blank" class="btn_b01" onclick="win_scrap(this.href); return false;">스크랩</a>
                </xsl:if>
                <xsl:if test="board_view/hrefs/good_href != ''">
                    <span class="bo_v_act_gng">
                        <a href="{board_view/hrefs/good_href}" id="good_button" class="btn_b01">추천 <strong><?php echo number_format($view['wr_good']) ?></strong></a>
                        <b id="bo_v_act_good"></b>
                    </span>
                </xsl:if>
                <xsl:if test="board_view/hrefs/nogood_href != ''">
                    <span class="bo_v_act_gng">
                        <a href="{board_view/hrefs/nogood_href}" id="nogood_button" class="btn_b01">비추천  <strong><?php echo number_format($view['wr_nogood']) ?></strong></a>
                        <b id="bo_v_act_nogood"></b>
                    </span>
                </xsl:if>
            </div>
        </xsl:when>
        <xsl:otherwise>
            <xsl:if test="board_view/board_info/use_good = 'true' or board_view/board_info/use_nogood = 'true'">
                <div id="bo_v_act">
                    <xsl:if test="board_view/board_info/use_good = 'true'">
                        <span>추천 <strong><xsl:value-of select="board_view/wr_good" /></strong></span>
                    </xsl:if>
                    <xsl:if test="board_view/board_info/use_good = 'true'">
                        <span>비추천 <strong><xsl:value-of select="board_view/wr_nogood" /></strong></span>
                    </xsl:if>
                </div>
            </xsl:if>
        </xsl:otherwise>
        </xsl:choose>
        <!-- } 스크랩 추천 비추천 끝 -->
    </section>
    <![CDATA[
    <?php
    include_once(G5_SNS_PATH."/view.sns.skin.php");
    ?>
    ]]>
    <?php
    // 코멘트 입출력
     ?>
<!--    <xsl:apply-templates select="/board_view/comments" /> -->
    <xsl:call-template name="comments" />


    <!-- 링크 버튼 시작 { -->
    <div id="bo_v_bot">
        <?php echo $link_buttons ?>
    </div>
    <!-- } 링크 버튼 끝 -->

</article>
<!-- } 게시판 읽기 끝 -->

<script>
<![CDATA[
<?php if ($board['bo_download_point'] < 0) { ?>
$(function() {
    $("a.view_file_download").click(function() {
        if(!g5_is_member) {
            alert("다운로드 권한이 없습니다.\n회원이시라면 로그인 후 이용해 보십시오.");
            return false;
        }

        var msg = "파일을 다운로드 하시면 포인트가 차감(<?php echo number_format($board['bo_download_point']) ?>점)됩니다.\n\n포인트는 게시물당 한번만 차감되며 다음에 다시 다운로드 하셔도 중복하여 차감하지 않습니다.\n\n그래도 다운로드 하시겠습니까?";

        if(confirm(msg)) {
            var href = $(this).attr("href")+"&js=on";
            $(this).attr("href", href);

            return true;
        } else {
            return false;
        }
    });
});
<?php } ?>

function board_move(href)
{
    window.open(href, "boardmove", "left=50, top=50, width=500, height=550, scrollbars=1");
}
]]>
</script>

<script>
<![CDATA[
$(function() {
    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });

    // 추천, 비추천
    $("#good_button, #nogood_button").click(function() {
        var $tx;
        if(this.id == "good_button")
            $tx = $("#bo_v_act_good");
        else
            $tx = $("#bo_v_act_nogood");

        excute_good(this.href, $(this), $tx);
        return false;
    });

    // 이미지 리사이즈
    $("#bo_v_atc").viewimageresize();
});

function excute_good(href, $el, $tx)
{
    $.post(
        href,
        { js: "on" },
        function(data) {
            if(data.error) {
                alert(data.error);
                return false;
            }

            if(data.count) {
                $el.find("strong").text(number_format(String(data.count)));
                if($tx.attr("id").search("nogood") > -1) {
                    $tx.text("이 글을 비추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                } else {
                    $tx.text("이 글을 추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                }
            }
        }, "json"
    );
}
]]>
</script>
<!-- } 게시글 읽기 끝 -->





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

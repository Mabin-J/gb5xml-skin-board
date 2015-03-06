<?php
include_once "common.skin.php";
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');
$captcha_html = "";
if ($is_guest && $board['bo_comment_level'] < 2) {
    $captcha_html = captcha_html('_comment');
    $captcha_html = str_replace("required", "required='true'", $captcha_html);
    $captcha_html = preg_replace("/<input[^<>]+/", "$0 /", $captcha_html);
    $captcha_html = preg_replace("/<img[^<>]+/", "$0 /", $captcha_html);
}

header('Content-Type: text/xsl');

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<xsl:stylesheet  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:template name="comments">
    <script>
    <![CDATA[
        // 글자수 제한
        var char_min = parseInt(<?php echo $comment_min ?>); // 최소
        var char_max = parseInt(<?php echo $comment_max ?>); // 최대
    ]]>
    </script>
    <section id="bo_vc">
        <h2>댓글목록</h2>
        <xsl:choose>
        <xsl:when test="board_view/comments/comment/*">
            <xsl:for-each select="board_view/comments/comment">
                <xsl:apply-templates select=".">
                    <xsl:with-param name="margin-left" select="0"/>
                </xsl:apply-templates>
            </xsl:for-each>
        </xsl:when>
        <xsl:otherwise>
            <p id="bo_vc_empty">등록된 댓글이 없습니다.</p>
        </xsl:otherwise>
        </xsl:choose>
    </section>
    <!-- } 댓글 끝 -->

    <xsl:if test="board_view/comments/comment_writable = 'true'">
        <!-- 댓글 쓰기 시작 { -->
        <aside id="bo_vc_w">
            <h2>댓글쓰기</h2>
            <form name="fviewcomment" action="./write_comment_update.php" onsubmit="return fviewcomment_submit(this);" method="post" autocomplete="off">
            <xsl:choose>
            <xsl:when test="board_view/other_params/w">
                <input type="hidden" name="w" value="{board_view/other_params/w}" id="w" />
            </xsl:when>
            <xsl:otherwise>
                <input type="hidden" name="w" value="c" id="w" />
            </xsl:otherwise>
            </xsl:choose>

            <input type="hidden" name="bo_table" value="{board_view/board_info/bo_table}" />
            <input type="hidden" name="wr_id" value="{board_view/wr_id}" />
            <input type="hidden" name="comment_id" value="{board_view/other_params/c_id}" id="comment_id" />
            <input type="hidden" name="sca" value="{board_view/search/sca}" />
            <input type="hidden" name="sfl" value="{board_view/search/sfl}" />
            <input type="hidden" name="stx" value="{board_view/search/stx}" />
            <input type="hidden" name="spt" value="{board_view/search/spt}" />
            <input type="hidden" name="page" value="{board_view/other_params/page}" />
            <input type="hidden" name="is_good" value="" />

            <div class="tbl_frm01 tbl_wrap">
                <table>
                <tbody>
                <?php if ($is_guest) { ?>
                <tr>
                    <th scope="row"><label for="wr_name">이름<strong class="sound_only"> 필수</strong></label></th>
                    <td><input type="text" name="wr_name" value="<?php echo get_cookie("ck_sns_name"); ?>" id="wr_name" required='true' class="frm_input required" size="5" maxLength="20" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="wr_password">비밀번호<strong class="sound_only"> 필수</strong></label></th>
                    <td><input type="password" name="wr_password" id="wr_password" required='true' class="frm_input required" size="10" maxLength="20" /></td>
                </tr>
                <?php } ?>
                <tr>
                    <th scope="row"><label for="wr_secret">비밀글사용</label></th>
                    <td><input type="checkbox" name="wr_secret" value="secret" id="wr_secret" /></td>
                </tr>
                <?php if ($is_guest) { ?>
                <tr>
                    <th scope="row">자동등록방지</th>
                    <td><?php echo $captcha_html; ?></td>
                </tr>
                <?php } ?>
                <?php
                if($board['bo_use_sns'] && ($config['cf_facebook_appid'] || $config['cf_twitter_key'])) {
                ?>
                <tr>
                    <th scope="row">SNS 동시등록</th>
                    <td id="bo_vc_send_sns"></td>
                </tr>
                <?php
                }
                ?>
                <tr>
                    <th scope="row">내용</th>
                    <td>
                        <?php if ($comment_min || $comment_max) { ?><strong id="char_cnt"><span id="char_count"></span>글자</strong><?php } ?>
                        <textarea id="wr_content" name="wr_content" maxlength="10000" required='true' class="required" title="내용"
                        <?php if ($comment_min || $comment_max) { ?>onkeyup="check_byte('wr_content', 'char_count');"<?php } ?>><xsl:value-of select="/board_view/other_params/c_wr_content" /></textarea>
                        <?php if ($comment_min || $comment_max) { ?><script> check_byte('wr_content', 'char_count'); </script><?php } ?>
                        <script>
                        <![CDATA[
                        $("textarea#wr_content[maxlength]").live("keyup change", function() {
                            var str = $(this).val()
                            var mx = parseInt($(this).attr("maxlength"))
                            if (str.length > mx) {
                                $(this).val(str.substr(0, mx));
                                return false;
                            }
                        });
                        ]]>
                        </script>
                    </td>
                </tr>
                </tbody>
                </table>
            </div>

            <div class="btn_confirm">
                <input type="submit" id="btn_submit" class="btn_submit" value="댓글등록"/>
            </div>

            </form>
        </aside>

        <script>
<![CDATA[
var save_before = '';
var save_html = document.getElementById('bo_vc_w').innerHTML;

function good_and_write()
{
    var f = document.fviewcomment;
    if (fviewcomment_submit(f)) {
        f.is_good.value = 1;
        f.submit();
    } else {
        f.is_good.value = 0;
    }
}

function fviewcomment_submit(f)
{
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자

    f.is_good.value = 0;

    var subject = "";
    var content = "";
    $.ajax({
        url: g5_bbs_url+"/ajax.filter.php",
        type: "POST",
        data: {
            "subject": "",
            "content": f.wr_content.value
        },
        dataType: "json",
        async: false,
        cache: false,
        success: function(data, textStatus) {
            subject = data.subject;
            content = data.content;
        }
    });

    if (content) {
        alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
        f.wr_content.focus();
        return false;
    }

    // 양쪽 공백 없애기
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자
    document.getElementById('wr_content').value = document.getElementById('wr_content').value.replace(pattern, "");
    if (char_min > 0 || char_max > 0)
    {
        check_byte('wr_content', 'char_count');
        var cnt = parseInt(document.getElementById('char_count').innerHTML);
        if (char_min > 0 && char_min > cnt)
        {
            alert("댓글은 "+char_min+"글자 이상 쓰셔야 합니다.");
            return false;
        } else if (char_max > 0 && char_max < cnt)
        {
            alert("댓글은 "+char_max+"글자 이하로 쓰셔야 합니다.");
            return false;
        }
    }
    else if (!document.getElementById('wr_content').value)
    {
        alert("댓글을 입력하여 주십시오.");
        return false;
    }

    if (typeof(f.wr_name) != 'undefined')
    {
        f.wr_name.value = f.wr_name.value.replace(pattern, "");
        if (f.wr_name.value == '')
        {
            alert('이름이 입력되지 않았습니다.');
            f.wr_name.focus();
            return false;
        }
    }

    if (typeof(f.wr_password) != 'undefined')
    {
        f.wr_password.value = f.wr_password.value.replace(pattern, "");
        if (f.wr_password.value == '')
        {
            alert('비밀번호가 입력되지 않았습니다.');
            f.wr_password.focus();
            return false;
        }
    }

    <?php if($is_guest) echo chk_captcha_js();  ?>

    document.getElementById("btn_submit").disabled = "disabled";

    return true;
}

function comment_box(comment_id, work)
{
    var el_id;
    // 댓글 아이디가 넘어오면 답변, 수정
    if (comment_id)
    {
        if (work == 'c')
            el_id = 'reply_' + comment_id;
        else
            el_id = 'edit_' + comment_id;
    }
    else
        el_id = 'bo_vc_w';

    if (save_before != el_id)
    {
        if (save_before)
        {
            document.getElementById(save_before).style.display = 'none';
            document.getElementById(save_before).innerHTML = '';
        }

        document.getElementById(el_id).style.display = '';
        document.getElementById(el_id).innerHTML = save_html;
        // 댓글 수정
        if (work == 'cu')
        {
            document.getElementById('wr_content').value = document.getElementById('save_comment_' + comment_id).value;
            if (typeof char_count != 'undefined')
                check_byte('wr_content', 'char_count');
            if (document.getElementById('secret_comment_'+comment_id).value)
                document.getElementById('wr_secret').checked = true;
            else
                document.getElementById('wr_secret').checked = false;
        }

        document.getElementById('comment_id').value = comment_id;
        document.getElementById('w').value = work;

        if(save_before)
            $("#captcha_reload").trigger("click");

        save_before = el_id;
    }
}

function comment_delete()
{
    return confirm("이 댓글을 삭제하시겠습니까?");
}

$(document).ready(function(){
    comment_box('', 'c'); // 댓글 입력폼이 보이도록 처리하기위해서 추가 (root님)
});

<?php if($board['bo_use_sns'] && ($config['cf_facebook_appid'] || $config['cf_twitter_key'])) { ?>
// sns 등록
$(function() {
    $("#bo_vc_send_sns").load(
        "<?php echo G5_SNS_URL; ?>/view_comment_write.sns.skin.php?bo_table=<?php echo $bo_table; ?>",
        function() {
            save_html = document.getElementById('bo_vc_w').innerHTML;
        }
    );
});
<?php } ?>
]]>
        </script>
        <!-- } 댓글 쓰기 끝 -->
    </xsl:if>
</xsl:template>



<xsl:template match="comment">
    <xsl:param name="margin-left" />
    <article id="c_{id}" style="margin-left: {$margin-left}px; border-top-color:#e0e0e0">
        <!-- 댓글 헤더 -->
        <header style="z-index: {/board_view/wr_comment - show_idx}">
            <h1><xsl:value-of select="wr_name" />님의 댓글</h1>
            <xsl:value-of select="name" />
            <xsl:if test="$margin-left &gt; 0">
                <img src="<?php echo $board_skin_url ?>/img/icon_reply.gif" class="icon_reply" alt="댓글의 댓글" />
            </xsl:if>
            <xsl:if test="/board_view/board_info/use_ip = 'true'">
                아이피
                <span class="bo_vc_hdinfo"><xsl:value-of select="ip" /></span>
            </xsl:if>
            작성일
            <span class="bo_vc_hdinfo"><time datetime="{datetime2}"><xsl:value-of select="datetime" /></time></span>
            <![CDATA[
            <?php
            include(G5_SNS_PATH.'/view_comment_list.sns.skin.php');
            ?>
            ]]>
        </header>
        <!-- 댓글 출력 -->
        <p>
            <xsl:if test="is_secret = 'true'">
                <img src="<?php echo $board_skin_url; ?>/img/icon_secret.gif" alt="비밀글" />
            </xsl:if>
            <xsl:copy-of select="content" />
        </p>
        <span id="edit_{id}"></span><!-- 수정 -->
        <span id="reply_{id}"></span><!-- 답변 -->
        <input type="hidden" value="{is_secret}" id="secret_comment_{id}" />
        <textarea id="save_comment_{id}" style="display:none"><xsl:value-of select="content1" /></textarea>
        <xsl:if test="can_reply = 'true' or can_edit = 'true' or can_delete = 
    'true'">
            <footer>
                <ul class="bo_vc_act">
                    <xsl:if test="can_reply = 'true'">
                        <li><a href="./board.php?{/board_view/query_string}&amp;c_id={id}&amp;w=c#bo_vc_w" onclick="comment_box('{id}', 'c'); return false;">답변</a></li>
                    </xsl:if>
                    <xsl:if test="can_edit = 'true'">
                        <li><a href="./board.php?{/board_view/query_string}&amp;c_id={id}&amp;w=cu#bo_vc_w" onclick="comment_box('{id}', 'cu'); return false;">수정</a></li>
                    </xsl:if>
                    <xsl:if test="can_delete = 'true'">
                        <li><a href="{del_link}" onclick="return comment_delete();">삭제</a></li>
                    </xsl:if>
                </ul>
            </footer>
        </xsl:if>
    </article>
    <xsl:if test="comments">
        <xsl:for-each select="comments/comment">
            <xsl:apply-templates select=".">
                <xsl:with-param name="margin-left" select="$margin-left + 20" />
            </xsl:apply-templates>
        </xsl:for-each>
    </xsl:if>
</xsl:template>
</xsl:stylesheet>

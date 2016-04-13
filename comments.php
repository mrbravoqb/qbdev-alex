<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/qb_classes/qb_post.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/qb_config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/common/qb_security.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/qb_classes/qb_member1.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/qb_widgets/comment_extra.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/qb_widgets/comment_reply.php');

class Comment
{
	function showComments($postID,$encryptedPostID){			
		
		include_once($_SERVER['DOCUMENT_ROOT'].'/includes/time_stamp.php');
		include($_SERVER['DOCUMENT_ROOT'].'/common/qb_session.php');
		$loggedin_member_id_for_comment = $_SESSION['SESS_MEMBER_ID'];
		$objPostExtra = new posts();
		$objMemberComments = new member1(); 				
		$viewAllComments=$objPostExtra ->viewCommentsByPostID($postID);
		$countOfAllComments=count($viewAllComments);
		$commentUser="";
		$rplCommentUser="";
		$deleteComment="";
		$deleteReply="";
		if($countOfAllComments>0){
			foreach($viewAllComments as $valueCurrentComment) {	
				$QbSecurityComment=new QB_SqlInjection();
				$objMemberCommentExtra = new comment_extra();
								
				$currentMemberUsername=$valueCurrentComment['username'];
				$commentMemberUserId=$valueCurrentComment['post_member_id'];
				$commentMemberProfileImage='';
				$commentMemberProfileImage=$objMemberComments->select_member_meta_value($commentMemberUserId,'current_profile_image');
				if($commentMemberProfileImage){			
						$commentMemberProfileImage=SITE_URL.'/'.$commentMemberProfileImage;	
				}
				else{
					$commentMemberProfileImage=SITE_URL.'/images/default.png';
				}				
				$commentId=$valueCurrentComment['comment_id'];				
				$commentContent=$valueCurrentComment['content'];
				$dateCommented=time_stamp_vj($valueCurrentComment['date_created']);				
				$encryptedcommentId=$QbSecurityComment->QB_AlphaID($commentId);
				$userLink='';
				$commentUser.='<div style="padding:5px;margin-bottom: 3px;background:#EDEDED;">';
				if($loggedin_member_id_for_comment == $commentMemberUserId)
				{
					$userLink=SITE_URL.'/i/'.$currentMemberUsername;				
					$deleteComment ='<div class="pull-right delComment"><a  id="'.$encryptedcommentId.'" class="delwallComment" href="javascript: void(0)" title="'.$lang['Delete update'].'" ><span class="glyphicon glyphicon-trash" aria-hidden="true" data-toggle="tooltip" data-placement="left" title="" data-original-title="Delete this comment.  "></span></a></div>';
				}
				else
				{
					$userLink=SITE_URL.'/'.$currentMemberUsername;					
				}
				$commentUser.='<div class="pull-left" style="width:15%">';				
				$commentUser.='<a title="'.$currentMemberUsername.'" href="'.$userLink.'" ><img style="width:100%"  src="'.$commentMemberProfileImage.'" /> </a> ';
				$commentUser.='</div>';
				$commentUser.='<div class="pull-left" style="width:85%; padding: 5px;">';
				$commentUser.=$deleteComment;
				$commentUser.='<div>';
				$commentUser.='<a href="'.$userLink.'"><b>'.$currentMemberUsername.'</b></a>';
				$commentUser.='</div>';
				$commentUser.='<div>';
				$commentUser.=$commentContent;				
				$commentUser.='</div>';
				$commentUser.='<div style="color: gray; font-size: 10px;margin-top:5px;margin-bottom:5px;">'.$dateCommented.'</div>';
				$commentUser.=$objMemberCommentExtra->extra_widget($commentId,$encryptedcommentId);				
				
				$commentUser.='<div class="replyContainer rplyCn'.$encryptedcommentId.'">';
				$objCommentReply = new CommentReply();
				$viewAllRplForComments=$objCommentReply->showReply($commentId,$encryptedcommentId,2);
				$commentUser.=$viewAllRplForComments;	
				$commentUser.='</div>';		
				$commentUser.='</div>';	
				$commentUser.='<div class="clearfix"></div>';	
					
				$commentUser.='</div>';
						
			}
		}

		$innerhtml='<div class="postsComments postComment'.$encryptedPostID.'" style="font-size:11px;">'.$commentUser.'</div>';

		return $innerhtml;
	}
	}

?>

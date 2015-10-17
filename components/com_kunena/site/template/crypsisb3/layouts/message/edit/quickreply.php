<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Message
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/** @var KunenaLayout $this */

/** @var KunenaForumMessage  $message  Message to reply to. */
$message = $this->message;

if (!$message->isAuthorised('reply'))
{
	return;
}

/** @var KunenaUser  $author  Author of the message. */
$author = isset($this->author) ? $this->author : $message->getAuthor();
/** @var KunenaForumTopic  $topic Topic of the message. */
$topic = isset($this->topic) ? $this->topic : $message->getTopic();
/** @var KunenaForumCategory  $category  Category of the message. */
$category = isset($this->category) ? $this->category : $message->getCategory();
/** @var KunenaConfig  $config  Kunena configuration. */
$config = isset($this->config) ? $this->config : KunenaFactory::getConfig();
/** @var KunenaUser  $me  Current user. */
$me = isset($this->me) ? $this->me : KunenaUserHelper::getMyself();

// Load caret.js always before atwho.js script and use it for autocomplete, emojiis...
$this->addStyleSheet('css/atwho.css');
$this->addScript('js/caret.js');
$this->addScript('js/atwho.js');
$this->addScript('js/edit.js');

if (KunenaFactory::getTemplate()->params->get('formRecover'))
{
	$this->addScript('js/sisyphus.js');
}
?>

<div class="modal fade" id="kreply<?php echo $message->displayField('id'); ?>_form" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" >
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic'); ?>" method="post"
				enctype="multipart/form-data" name="postform" id="postform" class="form-inline">
				<input type="hidden" name="task" value="post" />
				<input type="hidden" name="parentid" value="<?php echo $message->displayField('id'); ?>" />
				<input type="hidden" name="catid" value="<?php echo $category->displayField('id'); ?>" />
				<?php if (!$config->allow_change_subject): ?>
					<input type="hidden" name="subject" value="<?php echo $this->escape($this->message->subject); ?>" />
				<?php endif; ?>
				<?php echo JHtml::_('form.token'); ?>

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
					<h3>
						<?php echo JText::sprintf('COM_KUNENA_REPLYTO_X', $author->getLink()); ?>
					</h3>
				</div>

				<div class="modal-body">
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo JText::_('COM_KUNENA_GEN_NAME'); ?></label>
						<input type="text" name="authorname" size="35" class="inputbox form-control col-md-12" maxlength="35" value="<?php
						echo $this->escape($me->getName()); ?>" />
					</div>

					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?></label>
						<input type="text" id="subject" name="subject" size="35" class="inputbox form-control col-md-12"
						       maxlength="<?php echo (int) $config->maxsubject; ?>"
						       <?php if (!$config->allow_change_subject): ?>disabled<?php endif; ?>
						       value="<?php echo $message->displayField('subject'); ?>" />
					</div>

					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo JText::_('COM_KUNENA_GEN_MESSAGE'); ?></label>
						<textarea class="col-md-12 qreply form-control" id="kbbcode-message" name="message" rows="6" cols="60"></textarea>
					</div>

					<?php if ($topic->isAuthorised('subscribe')) : ?>
					<div class="clearfix"></div>
					<div class="control-group">
						<div class="controls">
							<input style="float: left; margin-right: 10px;" type="checkbox" name="subscribeMe" id="subscribeMe" value="1" <?php if ($config->subscriptionschecked == 1 && $me->canSubscribe != 0 || $config->subscriptionschecked == 0 && $me->canSubscribe == 1)
							{
								echo 'checked="checked"';
							} ?> />
							<label class="string optional" for="subscribeMe"><?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?></label>
						</div>
					</div>
					<?php endif; ?>
					<?php if ($me->exists() && $category->allow_anonymous) : ?>
						<input type="checkbox" id="kanonymous<?php echo $message->displayField('id'); ?>" name="anonymous"
							value="1" class="kinputbox postinput form-control" <?php if ($category->post_anonymous) echo 'checked="checked"'; ?> />
						<label for="kanonymous<?php echo intval($message->id); ?>">
							<?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC'); ?>
						</label>
					<?php endif; ?>
					<a href="index.php?option=com_kunena&view=topic&layout=reply&catid=<?php echo $message->catid;?>&id=<?php echo $message->thread;?>&mesid=<?php echo $message->id;?>&Itemid=<?php echo KunenaRoute::getItemID();?>" role="button" class="btn btn-default btn-small btn-link pull-right" rel="nofollow"><?php echo JText::_('COM_KUNENA_GO_TO_EDITOR'); ?></a>
				</div>
				<?php if (!empty($this->captchaEnabled)) : ?>
					<div class="control-group">
						<label class="control-label"><?php echo JText::_('COM_KUNENA_CAPDESC'); ?></label>
							<div class="controls"> <div id="dynamic_recaptcha_<?php echo $this->message->id; ?>"> </div> </div>
					</div>
				<?php endif; ?>
				<div class="modal-footer">
					<small><?php echo JText::_('COM_KUNENA_QMESSAGE_NOTE'); ?></small>
					<input type="submit" class="btn btn-primary kreply-submit" name="submit"
					       value="<?php echo JText::_('COM_KUNENA_SUBMIT'); ?>"
					       title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT')); ?>" />
					<?php //TODO: remove data on cancel. ?>
					<input type="reset" name="reset" class="btn btn-default"
						value="<?php echo (' ' . JText::_('COM_KUNENA_CANCEL') . ' ');?>"
						title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'));?>" data-dismiss="modal" aria-hidden="true" />
				</div>
				<input type="hidden" id="kurl_emojis" name="kurl_emojis" value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=listemoji&format=raw') ?>" />
				<input type="hidden" id="kemojis_allowed" name="kemojis_allowed" value="<?php echo $config->disemoticons ?>" />
			</form>
		</div>
	</div>
</div>

<?php
namespace easymedia\system\event\listener;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;

/**
 * JCoins listener for EasyMedia videos.
 *
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.easymedia.video
 */
class JCoinsEasyMediaVideoActionListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!MODULE_JCOINS) return;
		
		$action = $eventObj->getActionName();
		if ($action == 'triggerPublication') {
			foreach ($eventObj->getObjects() as $video) {
				if ($video->userID) {
					UserJCoinsStatementHandler::getInstance()->create('com.uz.jcoins.easymedia.video', $video->getDecoratedObject());
				}
			}
		}
		
		if ($action == 'restore') {
			foreach ($eventObj->getObjects() as $video) {
				if ($video->isPublished && !$video->isDisabled && $video->userID) {
					UserJCoinsStatementHandler::getInstance()->create('com.uz.jcoins.easymedia.video', $video->getDecoratedObject());
				}
			}
		}
		
		if ($action == 'disable' || $action == 'trash') {
			foreach ($eventObj->getObjects() as $video) {
				if ($video->isPublished && !$video->isDisabled && !$video->isDeleted && $video->userID) {
					UserJCoinsStatementHandler::getInstance()->revoke('com.uz.jcoins.easymedia.video', $video->getDecoratedObject());
				}
			}
		}
	}
}

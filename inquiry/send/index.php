<?php
/**	uqunie.com:/inquiry/send/index.php
 *
 * @package    uqunie.com
 * @copyright  uqunie Co., Ltd.
 */

/**	Namespace
 *
 */
namespace OP;

OP()->Unit()->WebPack()->Auto('app:/inquiry/css/inquiry.css', 'app:/inquiry/confirm/css/confirm.css');

$request = OP()->Request();
$token   = $request['mail_token'] ?? '';
$mail    = OP()->Session()->Get('uqunie_inquiry_mail', []);
$to      = OP()->isLocalhost() ? 'tomoaki@localhost': 'info@uqunie.com';
$valid   = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST'
	&& is_string($token)
	&& is_array($mail)
	&& isset($mail['token'], $mail['created_at'], $mail['to'], $mail['subject'], $mail['body'], $mail['reply_to'])
	&& is_string($mail['token'])
	&& hash_equals($mail['token'], $token)
	&& $mail['to'] === $to
	&& (time() - (int)$mail['created_at']) <= 1800;

$sent = false;
if( $valid ){
	//	Consume the token before invoking the mail transport to prevent duplicate submissions.
	OP()->Session()->Set('uqunie_inquiry_mail', null);
	$subject = function_exists('mb_encode_mimeheader')
		? mb_encode_mimeheader($mail['subject'], 'UTF-8', 'B', "\r\n")
		: $mail['subject'];
	$sent = OP()->Mail($to, $subject, $mail['body'], [
		'from'  => OP()->isLocalhost() ? 'tomoaki@localhost': 'info@uqunie.com',
		'reply' => $mail['reply_to'],
	]);
}
?>
<div class="inquiry-page confirm-page" data-translation="true">
	<nav class="inquiry-nav" aria-label="サイトナビゲーション">
		<a href="/">トップへ戻る</a>
	</nav>

	<header class="inquiry-hero confirm-hero">
		<p class="inquiry-eyebrow">CONTACT</p>
		<?php if($sent): ?>
			<h1>お問い合わせを送信しました。</h1>
			<p class="inquiry-lead">メールの送信処理を受け付けました。送信先は <?= htmlspecialchars($to, ENT_QUOTES, 'UTF-8') ?> です。</p>
		<?php elseif($valid): ?>
			<h1>メールを送信できませんでした。</h1>
			<p class="inquiry-lead">メールサーバーが送信を受け付けませんでした。入力画面から、もう一度お試しください。</p>
		<?php else: ?>
			<h1>この内容は送信できません。</h1>
			<p class="inquiry-lead">確認画面の有効期限が切れたか、すでに送信されています。入力画面から、もう一度お試しください。</p>
		<?php endif; ?>
	</header>

	<section class="confirm-empty">
		<?php if($sent): ?>
			<h2>送信を受け付けました</h2>
			<p>この画面を再読み込みしても、同じメールが再送信されることはありません。</p>
			<a class="confirm-button confirm-button-primary" href="/">トップページへ戻る</a>
		<?php else: ?>
			<h2>入力画面へ戻る</h2>
			<p>入力内容は保持されていません。恐れ入りますが、改めてご入力ください。</p>
			<a class="confirm-button confirm-button-primary" href="/inquiry/">お問い合わせを入力する</a>
		<?php endif; ?>
	</section>
</div>
<?php
//	Translation Service
if( OP()->Config('execute')['translation'] ?? false ){
	OP()->Template('translation.php');
	OP()->Template('translation.phtml');
}
?>

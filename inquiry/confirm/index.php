<?php
/**	uqunie.com:/inquiry/confirm/index.php
 *
 * @package    uqunie.com
 * @copyright  uqunie Co., Ltd.
 */

/**	Namespace
 *
 */
namespace OP;

//	Use the inquiry page styles and confirmation-specific additions.
OP()->Unit()->WebPack()->Auto('app:/inquiry/css/inquiry.css', './css/confirm.css');

//	OP()->Request() returns HTML-encoded values. Accept strings only.
$request = OP()->Request();
$value   = static function(string $key) use ($request) : string {
	$value = $request[$key] ?? '';
	return is_string($value) ? trim($value): '';
};

//	Convert durable request values into visitor-facing labels.
$labels = [
	'inquiry_type' => [
		'new-system'  => '新しいシステム',
		'budget-scope'=> '予算と実現範囲',
		'takeover'    => '開発途中の引き継ぎ',
		'improvement' => '稼働中システムの改善',
		'commerce'    => 'EC・予約・決済',
		'technical'   => '技術的な課題',
		'other'       => 'その他',
	],
	'budget' => [
		'undecided'   => 'まだ決めていない',
		'consultation'=> '相談して決めたい',
		'under-100'   => '100万円未満',
		'100-300'     => '100万〜300万円',
		'300-500'     => '300万〜500万円',
		'500-1000'    => '500万〜1,000万円',
		'over-1000'   => '1,000万円以上',
	],
	'schedule' => [
		'soon'        => 'できるだけ早く',
		'three-months'=> '3か月以内',
		'six-months'  => '6か月以内',
		'one-year'    => '1年以内',
		'undecided'   => '時期は決まっていない',
		'consultation'=> '相談して決めたい',
	],
	'system_status' => [
		'planning'      => '企画・要件整理中',
		'development'   => '開発途中',
		'before-release'=> '完成済み・公開前',
		'operating'     => '稼働中',
		'unknown'       => '分からない',
	],
	'source_code' => [
		'available'  => 'ある',
		'unavailable'=> 'ない',
		'unknown'    => '分からない',
	],
	'documents' => [
		'available'  => 'ある',
		'unavailable'=> 'ない',
		'unknown'    => '分からない',
	],
	'maintainer' => [
		'available'  => 'いる',
		'unavailable'=> 'いない',
		'unknown'    => '分からない',
	],
];
$display = static function(string $key) use ($labels, $value) : string {
	$stored = $value($key);
	return $labels[$key][$stored] ?? ($stored !== '' ? $stored: '—');
};

//	Check the minimum fields needed to create the mail preview.
$required = ['inquiry_type', 'organization', 'name', 'email', 'subject', 'message', 'privacy_consent'];
$ready    = true;
foreach( $required as $key ){
	if( $value($key) === '' ){
		$ready = false;
		break;
	}
}
$email = html_entity_decode($value('email'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
if(!filter_var($email, FILTER_VALIDATE_EMAIL) ){
	$ready = false;
}

//	These are returned to the input page only when the visitor asks to edit.
$field_keys = [
	'inquiry_type', 'organization', 'name', 'department', 'position', 'email', 'telephone', 'website',
	'subject', 'message', 'budget', 'schedule', 'system_status', 'technology', 'source_code', 'documents',
	'maintainer', 'privacy_consent',
];

//	Build exactly the same mail that will be handed to the mail transport.
$mail_to      = OP()->isLocalhost() ? 'tomoaki@localhost': 'info@uqunie.com';
$mail_subject = '';
$mail_body    = '';
$mail_token   = '';
if( $ready ){
	$plain = static function(string $key) use ($value) : string {
		return html_entity_decode($value($key), ENT_QUOTES | ENT_HTML5, 'UTF-8');
	};
	$mail_subject = preg_replace('/[\r\n]+/u', ' ', '[uqunie お問い合わせ] '.$plain('subject'));
	$mail_body = implode("\n", [
		'uqunie.comのお問い合わせフォームから、以下の内容を受け付けました。',
		'',
		'■ ご相談の種類',
		$display('inquiry_type'),
		'',
		'■ お客様について',
		'会社・組織名: '.$plain('organization'),
		'お名前: '.$plain('name'),
		'部署名: '.($plain('department') ?: '—'),
		'役職: '.($plain('position') ?: '—'),
		'メールアドレス: '.$email,
		'電話番号: '.($plain('telephone') ?: '—'),
		'Webサイト: '.($plain('website') ?: '—'),
		'',
		'■ ご相談内容',
		'ご相談の概要: '.$plain('subject'),
		'ご予算: '.$display('budget'),
		'ご希望の時期: '.$display('schedule'),
		'',
		'現在の状況と解決したいこと:',
		$plain('message'),
		'',
		'■ 既存システムについて',
		'現在の状態: '.$display('system_status'),
		'主な使用技術: '.($plain('technology') ?: '—'),
		'ソースコード: '.$display('source_code'),
		'仕様書・設計資料: '.$display('documents'),
		'現在の保守担当者: '.$display('maintainer'),
		'',
		'■ 同意内容',
		'個人情報の取り扱い: 同意する',
	]);

	$mail_token = bin2hex(random_bytes(24));
	OP()->Session()->Set('uqunie_inquiry_mail', [
		'token'      => $mail_token,
		'created_at' => time(),
		'to'         => $mail_to,
		'subject'    => $mail_subject,
		'body'       => $mail_body,
		'reply_to'   => $email,
	]);
}
?>
<div class="inquiry-page confirm-page" data-translation="true">
	<nav class="inquiry-nav" aria-label="サイトナビゲーション">
		<a href="/">トップへ戻る</a>
	</nav>

	<header class="inquiry-hero confirm-hero">
		<p class="inquiry-eyebrow">CONFIRM</p>
		<h1>入力内容をご確認ください。</h1>
		<p class="inquiry-lead">まだ送信されていません。入力内容と、実際に送信されるメールをご確認ください。</p>
	</header>

	<?php if(!$ready): ?>
		<section class="confirm-empty" aria-labelledby="confirm-empty-title">
			<h2 id="confirm-empty-title">確認する内容が不足しています。</h2>
			<p>入力画面で必須項目を入力してから、確認画面へ進んでください。</p>
			<a class="confirm-button confirm-button-primary" href="/inquiry/">入力画面へ戻る</a>
		</section>
	<?php else: ?>
		<div class="confirm-layout">
			<section class="confirm-content" aria-label="お問い合わせ内容">
				<div class="confirm-section">
					<h2><span>01</span>ご相談の種類</h2>
					<dl class="confirm-list">
						<div><dt>ご相談の種類</dt><dd><?= $display('inquiry_type') ?></dd></div>
					</dl>
				</div>

				<div class="confirm-section">
					<h2><span>02</span>お客様について</h2>
					<dl class="confirm-list">
						<div><dt>会社・組織名</dt><dd><?= $display('organization') ?></dd></div>
						<div><dt>お名前</dt><dd><?= $display('name') ?></dd></div>
						<div><dt>部署名</dt><dd><?= $display('department') ?></dd></div>
						<div><dt>役職</dt><dd><?= $display('position') ?></dd></div>
						<div><dt>メールアドレス</dt><dd><?= $display('email') ?></dd></div>
						<div><dt>電話番号</dt><dd><?= $display('telephone') ?></dd></div>
						<div><dt>Webサイト</dt><dd><?= $display('website') ?></dd></div>
					</dl>
				</div>

				<div class="confirm-section">
					<h2><span>03</span>ご相談内容</h2>
					<dl class="confirm-list">
						<div><dt>ご相談の概要</dt><dd><?= $display('subject') ?></dd></div>
						<div class="confirm-message"><dt>現在の状況と解決したいこと</dt><dd><?= nl2br($value('message'), false) ?></dd></div>
						<div><dt>ご予算</dt><dd><?= $display('budget') ?></dd></div>
						<div><dt>ご希望の時期</dt><dd><?= $display('schedule') ?></dd></div>
					</dl>
				</div>

				<div class="confirm-section">
					<h2><span>04</span>既存システムについて</h2>
					<dl class="confirm-list">
						<div><dt>現在の状態</dt><dd><?= $display('system_status') ?></dd></div>
						<div><dt>主な使用技術</dt><dd><?= $display('technology') ?></dd></div>
						<div><dt>ソースコード</dt><dd><?= $display('source_code') ?></dd></div>
						<div><dt>仕様書・設計資料</dt><dd><?= $display('documents') ?></dd></div>
						<div><dt>現在の保守担当者</dt><dd><?= $display('maintainer') ?></dd></div>
					</dl>
				</div>

				<div class="confirm-section">
					<h2><span>05</span>同意内容</h2>
					<dl class="confirm-list">
						<div><dt>個人情報の取り扱い</dt><dd><?= $value('privacy_consent') === '1' ? '同意する': '—' ?></dd></div>
					</dl>
				</div>

				<div class="confirm-section mail-preview">
					<h2><span>MAIL</span>送信されるメール</h2>
					<dl class="confirm-list">
						<div><dt>宛先</dt><dd><?= htmlspecialchars($mail_to, ENT_QUOTES, 'UTF-8') ?></dd></div>
						<div><dt>返信先</dt><dd><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></dd></div>
						<div><dt>件名</dt><dd><?= htmlspecialchars($mail_subject, ENT_QUOTES, 'UTF-8') ?></dd></div>
					</dl>
					<pre><?= htmlspecialchars($mail_body, ENT_QUOTES, 'UTF-8') ?></pre>
				</div>
			</section>

			<aside class="confirm-actions" aria-label="確認画面の操作">
				<div class="confirm-action-card">
					<h2>内容を修正しますか？</h2>
					<p>「入力内容を修正する」で、入力済みの内容を保持したまま戻れます。</p>
					<form action="/inquiry/" method="post">
						<?php foreach( $field_keys as $key ): ?>
							<?php if( $value($key) !== '' ): ?>
								<input type="hidden" name="<?= $key ?>" value="<?= $value($key) ?>">
							<?php endif; ?>
						<?php endforeach; ?>
						<button class="confirm-button confirm-button-secondary" type="submit">入力内容を修正する</button>
					</form>
					<form action="/inquiry/send/" method="post">
						<input type="hidden" name="mail_token" value="<?= htmlspecialchars($mail_token, ENT_QUOTES, 'UTF-8') ?>">
						<button class="confirm-button confirm-button-send" type="submit">この内容で送信する</button>
					</form>
					<p class="confirm-send-note">送信後、この画面から同じ内容を再送信することはできません。</p>
				</div>
			</aside>
		</div>
	<?php endif; ?>
</div>
<?php
//	Translation Service
if( OP()->Config('execute')['translation'] ?? false ){
	OP()->Template('translation.php');
	OP()->Template('translation.phtml');
}
?>

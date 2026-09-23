<?php
/**	uqunie.com:/inquiry/index.php
 *
 * @package    uqunie.com
 * @copyright  uqunie Co., Ltd.
 */

/**	Namespace
 *
 */
namespace OP;

//	This page posts to the confirmation preview. No mail is sent here.
OP()->Unit()->WebPack()->Auto('./css/inquiry.css');

//	Restore values when the visitor returns from the confirmation preview.
$request = OP()->Request();
$value   = static function(string $key) use ($request) : string {
	$value = $request[$key] ?? '';
	return is_string($value) ? $value: '';
};
$is = static function(string $key, string $expected) use ($value) : bool {
	return $value($key) === $expected;
};
?>
<div class="inquiry-page" data-translation="true">
	<nav class="inquiry-nav" aria-label="サイトナビゲーション">
		<a href="/">トップへ戻る</a>
	</nav>

	<header class="inquiry-hero">
		<p class="inquiry-eyebrow">CONTACT</p>
		<h1>作るものが決まっていなくても、<br>ご相談ください。</h1>
		<p class="inquiry-lead">
			目的、予算、現在の課題を伺い、何を確認し、どこから始めるべきかを整理します。
			新規構築だけでなく、開発途中や稼働中のシステムについてもご相談いただけます。
		</p>
	</header>

	<div class="prototype-notice" role="status">
		<strong>送信前に内容をご確認いただけます</strong>
		<span>確認画面では、実際に送られるメール本文も表示します。</span>
	</div>

	<div class="inquiry-layout">
		<div class="inquiry-main">
			<form class="inquiry-form" action="/inquiry/confirm/" method="post">
				<fieldset>
					<legend><span>01</span>ご相談の種類</legend>
					<p class="field-intro">最も近いものを一つ選んでください。</p>

					<div class="choice-grid">
						<label class="choice-card">
							<input type="radio" name="inquiry_type" value="new-system"<?= $is('inquiry_type', 'new-system') ? ' checked': '' ?> required>
							<span><strong>新しいシステム</strong><small>企画・要件整理から相談したい</small></span>
						</label>
						<label class="choice-card">
							<input type="radio" name="inquiry_type" value="budget-scope"<?= $is('inquiry_type', 'budget-scope') ? ' checked': '' ?>>
							<span><strong>予算と実現範囲</strong><small>予算内で機能を選びたい</small></span>
						</label>
						<label class="choice-card">
							<input type="radio" name="inquiry_type" value="takeover"<?= $is('inquiry_type', 'takeover') ? ' checked': '' ?>>
							<span><strong>開発途中の引き継ぎ</strong><small>進行中の案件を相談したい</small></span>
						</label>
						<label class="choice-card">
							<input type="radio" name="inquiry_type" value="improvement"<?= $is('inquiry_type', 'improvement') ? ' checked': '' ?>>
							<span><strong>稼働中システムの改善</strong><small>保守・改修・運用を相談したい</small></span>
						</label>
						<label class="choice-card">
							<input type="radio" name="inquiry_type" value="commerce"<?= $is('inquiry_type', 'commerce') ? ' checked': '' ?>>
							<span><strong>EC・予約・決済</strong><small>販売や決済について相談したい</small></span>
						</label>
						<label class="choice-card">
							<input type="radio" name="inquiry_type" value="technical"<?= $is('inquiry_type', 'technical') ? ' checked': '' ?>>
							<span><strong>技術的な課題</strong><small>設計や調査について相談したい</small></span>
						</label>
						<label class="choice-card">
							<input type="radio" name="inquiry_type" value="other"<?= $is('inquiry_type', 'other') ? ' checked': '' ?>>
							<span><strong>その他</strong><small>上記に当てはまらないご相談</small></span>
						</label>
					</div>
				</fieldset>

				<fieldset>
					<legend><span>02</span>お客様について</legend>

					<div class="field-grid field-grid-two">
						<div class="form-field">
							<label for="organization">会社・組織名 <em>必須</em></label>
							<input id="organization" name="organization" type="text" autocomplete="organization" placeholder="株式会社サンプル" value="<?= $value('organization') ?>" required>
						</div>
						<div class="form-field">
							<label for="name">お名前 <em>必須</em></label>
							<input id="name" name="name" type="text" autocomplete="name" placeholder="山田 太郎" value="<?= $value('name') ?>" required>
						</div>
						<div class="form-field">
							<label for="department">部署名 <span>任意</span></label>
							<input id="department" name="department" type="text" autocomplete="organization-title" placeholder="経営企画部" value="<?= $value('department') ?>">
						</div>
						<div class="form-field">
							<label for="position">役職 <span>任意</span></label>
							<input id="position" name="position" type="text" placeholder="部長" value="<?= $value('position') ?>">
						</div>
						<div class="form-field">
							<label for="email">メールアドレス <em>必須</em></label>
							<input id="email" name="email" type="email" autocomplete="email" inputmode="email" placeholder="name@example.com" value="<?= $value('email') ?>" required>
						</div>
						<div class="form-field">
							<label for="telephone">電話番号 <span>任意</span></label>
							<input id="telephone" name="telephone" type="tel" autocomplete="tel" inputmode="tel" placeholder="03-0000-0000" value="<?= $value('telephone') ?>">
						</div>
					</div>

					<div class="form-field">
						<label for="website">会社・サービスのWebサイト <span>任意</span></label>
						<input id="website" name="website" type="url" autocomplete="url" inputmode="url" placeholder="https://example.com/" value="<?= $value('website') ?>">
					</div>
				</fieldset>

				<fieldset>
					<legend><span>03</span>ご相談内容</legend>

					<div class="form-field">
						<label for="subject">ご相談の概要 <em>必須</em></label>
						<input id="subject" name="subject" type="text" placeholder="例：既存ECサイトの保守会社を変更したい" value="<?= $value('subject') ?>" required>
					</div>

					<div class="form-field">
						<label for="message">現在の状況と解決したいこと <em>必須</em></label>
						<textarea id="message" name="message" rows="9" placeholder="分かっている範囲で構いません。現在の状況、困っていること、実現したいことなどをご記入ください。" required><?= $value('message') ?></textarea>
						<p class="field-help">仕様や機能が決まっていなくても、ご相談いただけます。</p>
					</div>

					<div class="field-grid field-grid-two">
						<div class="form-field">
							<label for="budget">ご予算 <span>任意</span></label>
							<select id="budget" name="budget">
								<option value="">選択してください</option>
								<option value="undecided"<?= $is('budget', 'undecided') ? ' selected': '' ?>>まだ決めていない</option>
								<option value="consultation"<?= $is('budget', 'consultation') ? ' selected': '' ?>>相談して決めたい</option>
								<option value="under-100"<?= $is('budget', 'under-100') ? ' selected': '' ?>>100万円未満</option>
								<option value="100-300"<?= $is('budget', '100-300') ? ' selected': '' ?>>100万〜300万円</option>
								<option value="300-500"<?= $is('budget', '300-500') ? ' selected': '' ?>>300万〜500万円</option>
								<option value="500-1000"<?= $is('budget', '500-1000') ? ' selected': '' ?>>500万〜1,000万円</option>
								<option value="over-1000"<?= $is('budget', 'over-1000') ? ' selected': '' ?>>1,000万円以上</option>
							</select>
							<p class="field-help">価格を合わせるためではなく、実現範囲を検討するために伺います。</p>
						</div>
						<div class="form-field">
							<label for="schedule">ご希望の時期 <span>任意</span></label>
							<select id="schedule" name="schedule">
								<option value="">選択してください</option>
								<option value="soon"<?= $is('schedule', 'soon') ? ' selected': '' ?>>できるだけ早く</option>
								<option value="three-months"<?= $is('schedule', 'three-months') ? ' selected': '' ?>>3か月以内</option>
								<option value="six-months"<?= $is('schedule', 'six-months') ? ' selected': '' ?>>6か月以内</option>
								<option value="one-year"<?= $is('schedule', 'one-year') ? ' selected': '' ?>>1年以内</option>
								<option value="undecided"<?= $is('schedule', 'undecided') ? ' selected': '' ?>>時期は決まっていない</option>
								<option value="consultation"<?= $is('schedule', 'consultation') ? ' selected': '' ?>>相談して決めたい</option>
							</select>
							<p class="field-help">ご希望を伺うもので、納期を確約する項目ではありません。</p>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<legend><span>04</span>既存システムについて <small>該当する場合のみ</small></legend>

					<div class="field-grid field-grid-two">
						<div class="form-field">
							<label for="system_status">現在の状態 <span>任意</span></label>
							<select id="system_status" name="system_status">
								<option value="">選択してください</option>
								<option value="planning"<?= $is('system_status', 'planning') ? ' selected': '' ?>>企画・要件整理中</option>
								<option value="development"<?= $is('system_status', 'development') ? ' selected': '' ?>>開発途中</option>
								<option value="before-release"<?= $is('system_status', 'before-release') ? ' selected': '' ?>>完成済み・公開前</option>
								<option value="operating"<?= $is('system_status', 'operating') ? ' selected': '' ?>>稼働中</option>
								<option value="unknown"<?= $is('system_status', 'unknown') ? ' selected': '' ?>>分からない</option>
							</select>
						</div>
						<div class="form-field">
							<label for="technology">主な使用技術 <span>任意</span></label>
							<input id="technology" name="technology" type="text" placeholder="分からない場合は空欄で構いません" value="<?= $value('technology') ?>">
						</div>
					</div>

					<div class="availability-grid">
						<fieldset class="availability-field">
							<legend>ソースコード</legend>
							<label><input type="radio" name="source_code" value="available"<?= $is('source_code', 'available') ? ' checked': '' ?>> ある</label>
							<label><input type="radio" name="source_code" value="unavailable"<?= $is('source_code', 'unavailable') ? ' checked': '' ?>> ない</label>
							<label><input type="radio" name="source_code" value="unknown"<?= $is('source_code', 'unknown') ? ' checked': '' ?>> 分からない</label>
						</fieldset>
						<fieldset class="availability-field">
							<legend>仕様書・設計資料</legend>
							<label><input type="radio" name="documents" value="available"<?= $is('documents', 'available') ? ' checked': '' ?>> ある</label>
							<label><input type="radio" name="documents" value="unavailable"<?= $is('documents', 'unavailable') ? ' checked': '' ?>> ない</label>
							<label><input type="radio" name="documents" value="unknown"<?= $is('documents', 'unknown') ? ' checked': '' ?>> 分からない</label>
						</fieldset>
						<fieldset class="availability-field">
							<legend>現在の保守担当者</legend>
							<label><input type="radio" name="maintainer" value="available"<?= $is('maintainer', 'available') ? ' checked': '' ?>> いる</label>
							<label><input type="radio" name="maintainer" value="unavailable"<?= $is('maintainer', 'unavailable') ? ' checked': '' ?>> いない</label>
							<label><input type="radio" name="maintainer" value="unknown"<?= $is('maintainer', 'unknown') ? ' checked': '' ?>> 分からない</label>
						</fieldset>
					</div>
				</fieldset>

				<fieldset>
					<legend><span>05</span>送信前の確認</legend>
					<div class="security-note">
						<strong>機密情報を入力しないでください</strong>
						<p>パスワード、秘密鍵、アクセストークン、クレジットカード情報、個人情報を含むログなどは記載しないでください。資料の受け渡し方法は、ご相談内容を確認した後にご案内します。</p>
					</div>

					<label class="consent-field">
						<input type="checkbox" name="privacy_consent" value="1"<?= $is('privacy_consent', '1') ? ' checked': '' ?> required>
						<span>個人情報の取り扱いに同意します。 <em>必須</em></span>
					</label>

					<p class="agreement-note">お問い合わせの受付をもって、対応、契約、納期、費用などを確約するものではありません。内容によっては対応できない場合があります。</p>

					<div class="submit-area">
						<button type="submit">入力内容を確認する</button>
						<p>確認画面へ進みます。この時点では送信されません。</p>
					</div>
				</fieldset>
			</form>
		</div>

		<aside class="inquiry-aside">
			<div class="aside-card">
				<p class="inquiry-eyebrow">BEFORE YOU START</p>
				<h2>分かる範囲で<br>ご記入ください。</h2>
				<p>仕様書がない、担当者が不在、現在の構成が分からない場合も、その状態からご相談いただけます。</p>
			</div>
			<div class="aside-points">
				<p><span>01</span>作るものが決まっていなくても相談可能</p>
				<p><span>02</span>予算から実現範囲を一緒に検討</p>
				<p><span>03</span>開発途中・稼働中の引き継ぎにも対応</p>
			</div>
		</aside>
	</div>
</div>
<?php
//	Translation Service
if( OP()->Config('execute')['translation'] ?? false ){
	OP()->Template('translation.php');
	OP()->Template('translation.phtml');
}
?>

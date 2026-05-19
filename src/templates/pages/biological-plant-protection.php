<?php

declare(strict_types=1);

$dictionary = isset($dictionary) && is_array($dictionary) ? $dictionary : [];

$pagePlantProtectionPayload = isset($pageContent) && is_array($pageContent) ? $pageContent : [];
$aboutUsHtml = isset($pagePlantProtectionPayload['aboutUs']) && is_string($pagePlantProtectionPayload['aboutUs'])
	? trim($pagePlantProtectionPayload['aboutUs'])
	: '';
$topBoldTextHtml = isset($pagePlantProtectionPayload['topBoldText']) && is_string($pagePlantProtectionPayload['topBoldText'])
	? trim($pagePlantProtectionPayload['topBoldText'])
	: '';
$topPictureTextHtml = isset($pagePlantProtectionPayload['topPictureText']) && is_string($pagePlantProtectionPayload['topPictureText'])
	? trim($pagePlantProtectionPayload['topPictureText'])
	: '';

$listOfObjectives = [];
if (isset($pagePlantProtectionPayload['listOfObjectives']) && is_array($pagePlantProtectionPayload['listOfObjectives'])) {
	foreach ($pagePlantProtectionPayload['listOfObjectives'] as $objectiveItem) {
		if (is_string($objectiveItem) && trim($objectiveItem) !== '') {
			$listOfObjectives[] = trim($objectiveItem);
		}
	}
}
$hasListOfObjectivesSection = $listOfObjectives !== [];

$listOfObjectivesFigureJpg = '/img/plant-protection/list_of_object.jpg';
$listOfObjectivesFigureWebp = '/img/plant-protection/list_of_object.webp';
$plantProtectionObjectivesFigureStyle = '--plant-protection-objectives-bg-jpg: url(\''
	. htmlspecialchars($listOfObjectivesFigureJpg, ENT_QUOTES, 'UTF-8')
	. '\');--plant-protection-objectives-bg-webp: url(\''
	. htmlspecialchars($listOfObjectivesFigureWebp, ENT_QUOTES, 'UTF-8')
	. '\');';

$hasRenderableTopPictures = true;

$hasFollowContent = $topBoldTextHtml !== '' || $topPictureTextHtml !== '' || $hasRenderableTopPictures;
$hasAboutSection = $aboutUsHtml !== '';
?>
<?php if ($hasAboutSection): ?>
	<section class="oil-content-section">
		<h2 class="section-title"><?= $dictionary['aboutUs']; ?></h2>
		<div class="oil-content-section__body oil-content-section__body--justified"><?= $aboutUsHtml; ?></div>
	</section>
<?php endif; ?>
<?php if ($hasFollowContent): ?>
	<div class="plant-protection-follow">
		<?php if ($topBoldTextHtml !== ''): ?>
			<div class="plant-protection-follow__lead"><?= $topBoldTextHtml; ?></div>
		<?php endif; ?>
		<?php if ($topPictureTextHtml !== ''): ?>
			<div><?= $topPictureTextHtml; ?></div>
		<?php endif; ?>
		<?php require TEMPLATES_PATH . '/partials/biological-plant-protection/biological-plant-protection-top-pictures.php'; ?>
	</div>
<?php endif; ?>
<?php if ($hasListOfObjectivesSection): ?>
	<section class="plant-protection-objectives">
		<h2 class="section-title"><?= $dictionary['listOfObjectives']; ?></h2>
		<div class="plant-protection-objectives__layout">
			<ul class="plant-protection-objectives__list">
				<?php foreach ($listOfObjectives as $objectiveLine): ?>
					<li class="plant-protection-objectives__item"><?= htmlspecialchars($objectiveLine, ENT_QUOTES, 'UTF-8'); ?></li>
				<?php endforeach; ?>
			</ul>
			<div
				class="plant-protection-objectives__figure plant-protection-objectives__figure--with-bg"
				style="<?= $plantProtectionObjectivesFigureStyle; ?>"
				aria-hidden="true"></div>
		</div>
	</section>
<?php endif; ?>
<section class="plant-protection-strategy">
	<h2 class="section-title"><?= $dictionary['strategy']; ?></h2>
	<?php
	$plantProtectionStrategy = $pagePlantProtectionPayload['strategy'];
	require TEMPLATES_PATH . '/partials/biological-plant-protection/biological-plant-protection-strategy.php';
	?>
</section>
<section class="plant-protection-beneficial-microflora">
	<h2 class="section-title"><?= $pagePlantProtectionPayload['beneficialMicrofloraPathogensScheme']['title']; ?></h2>
	<?php
	$plantProtectionBeneficialMicroflora = $pagePlantProtectionPayload['beneficialMicrofloraPathogensScheme'];
	require TEMPLATES_PATH . '/partials/biological-plant-protection/biological-plant-protection-beneficial-microflora.php';
	?>
</section>
<?php
$parasiticFungiVsPestsCards = $pagePlantProtectionPayload['parasiticFungiVsPestsCards'];
$parasiticFungiVsPestsCardItems = [
	[
		'image' => '/img/plant-protection/parasitic-fungi-pests/infection.svg',
		'title' => $parasiticFungiVsPestsCards['infectionTitle'],
		'text' => $parasiticFungiVsPestsCards['infectionText'],
	],
	[
		'image' => '/img/plant-protection/parasitic-fungi-pests/parasitism.svg',
		'title' => $parasiticFungiVsPestsCards['parasitismTitle'],
		'text' => $parasiticFungiVsPestsCards['parasitismText'],
	],
	[
		'image' => '/img/plant-protection/parasitic-fungi-pests/saprophytic_phase.svg',
		'title' => $parasiticFungiVsPestsCards['saprophyticPhaseTitle'],
		'text' => $parasiticFungiVsPestsCards['saprophyticPhaseText'],
	],
];
?>
<section class="plant-protection-parasitic-fungi">
	<h2 class="section-title"><?= $parasiticFungiVsPestsCards['title']; ?></h2>
	<div class="parasitic-fungi-cards">
		<?php foreach ($parasiticFungiVsPestsCardItems as $parasiticFungiCard): ?>
			<article class="parasitic-fungi-cards__card">
				<div class="parasitic-fungi-cards__visual">
					<img
						class="parasitic-fungi-cards__img"
						src="<?= htmlspecialchars($parasiticFungiCard['image'], ENT_QUOTES, 'UTF-8'); ?>"
						alt=""
						width="220"
						height="221"
						decoding="async" />
				</div>
				<div class="parasitic-fungi-cards__body">
					<h3 class="parasitic-fungi-cards__title"><?= $parasiticFungiCard['title']; ?>:</h3>
					<div class="parasitic-fungi-cards__text"><?= $parasiticFungiCard['text']; ?></div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>
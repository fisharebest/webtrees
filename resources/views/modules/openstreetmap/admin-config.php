<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= view('admin/breadcrumbs', [
		'links' => [route('admin-control-panel') => I18N::translate('Control panel'),
		            route('admin-modules') => I18N::translate('Module administration'),
					$title
		]]) ?>

<form method="POST" name="configform" action="<?=e(route('admin-module', ['module' => $module, 'action' => 'AdminUpdateConfig'])) ?>">
	<?= csrf_field() ?>

	<div class="form-group row">
		<div class="col-sm-3 col-form-label">
			<?= I18N::translate('Geographic data') ?>
		</div>
		<div class="col-sm-9">
			<a class="btn btn-primary"
			   href="<?= e(route('admin-module', ['module' => $module, 'action' => 'AdminPlaces'])) ?>">
					<?= FontAwesome::decorativeIcon('edit') ?>
					<?= I18N::translate('edit') ?>
			</a>
		</div>
	</div>

	<h4><?= I18N::translate('General') ?></h4>

	<!-- Providers -->
	<fieldset class="form-group">
		<div class="form-row">
			<legend class="col-form-label col-sm-3">
				<?= I18N::translate('Map provider and style') ?>
			</legend>
			<div class="col-sm-9">
				<div class="form-row">
					<div class="col-sm-6">
						<div class="input-group">
							<div class="input-group-prepend">
								<label class="input-group-text" for="provider">
									<?= I18N::translate('Provider') ?>
								</label>
							</div>
							<?= Bootstrap4::select(
								$provider['providers'],
								$provider['selectedProv'],
								['id' => 'provider', 'name' => 'provider']
							) ?>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="input-group">
							<div class="input-group-prepend">
								<label class="input-group-text" for="provider_style">
									<?= I18N::translate('Style') ?>
								</label>
							</div>
							<?= Bootstrap4::select(
								$provider['styles'],
								$provider['selectedStyle'],
								['id' => 'provider_style', 'name' => 'provider_style']
							) ?>
						</div>
					</div>
				</div>
				<p class="small text-muted"><?= I18N::translate('Select map provider and style') ?></p>
			</div>
		</div>
	</fieldset>

	<!-- Animate map reset-->
	<fieldset class="form-group">
		<div class="form-row">
			<legend class="col-form-label col-sm-3">
				<?= I18N::translate('Animate map reset') ?>
			</legend>
			<div class="col-sm-9">
				<div class="form-row">
					<div class="col-sm-4">
						<div class="input-group">
							<?= Bootstrap4::radioButtons(
								'map_animate',
								[I18N::translate('no'), I18N::translate('yes')],
								$animate,
								true
							) ?>
						</div>
					</div>
				</div>
			</div>
		</Div>
	</fieldset>

	<!-- Place Hierarchy -->
	<fieldset class="form-group">
		<div class="form-row">
			<legend class="col-form-label col-sm-3">
				<?= I18N::translate('Use a map for the place hierarchy') ?>
			</legend>
			<div class="col-sm-9">
				<div class="form-row">
					<div class="col-sm-4">
						<div class="input-group">
							<?= Bootstrap4::radioButtons(
								'place_hierarchy',
								[I18N::translate('no'), I18N::translate('yes')],
								$hierarchy,
								true
							) ?>
						</div>
					</div>
				</div>
			</div>
		</Div>
	</fieldset>

	<h4><?= I18N::translate('Provider credentials') ?></h4>

	<!-- Mapbox -->
	<fieldset class="form-group">
		<div class="form-row">
			<legend class="col-form-label col-sm-3">
				Mapbox
			</legend>
			<div class="col-sm-9">
				<div class="form-row">
					<div class="col-sm-3">
						<div class="input-group">
							<div class="input-group-prepend">
								<label class="input-group-text" for="mapbox_id">
									<?= I18N::translate('Id') ?>
								</label>
							</div>
							<input id="mapbox_id" class="form-control" type="text" name="mapbox_id"
								   value="<?= $mapboxId ?>">
						</div>
					</div>
					<div class="col-sm-9">
						<div class="input-group">
							<div class="input-group-prepend">
								<label class="input-group-text" for="mapbox_token">
									<?= I18N::translate('Token') ?>
								</label>
							</div>
							<input id="mapbox_token" class="form-control" type="text"
								   name="mapbox_token"
								   value="<?= $mapboxToken ?>">
						</div>
					</div>
				</div>
				<p class="small text-muted"><?= I18N::translate(
						'Mapbox&copy; requires that you register and obtain an ID and token before the service can be used'
					) ?>
					<a href="https://www.mapbox.com/studio/"><?= I18N::translate('Get Mapbox codes') ?></a>
				</p>
			</div>
		</div>
	</fieldset>


	<!-- HERE -->
	<fieldset class="form-group">
		<div class="form-row">
			<legend class="col-form-label col-sm-3">
				HERE WeGo
			</legend>
			<div class="col-sm-9">
				<div class="form-row">
					<div class="col-sm-6">
						<div class="input-group">
							<div class="input-group-prepend">
								<label class="input-group-text" for="here_appid">
									<?= I18N::translate('App Id') ?>
								</label>
							</div>
							<input id="here_appid" class="form-control" type="text" name="here_appid"
								   value="<?= $here_Appid ?>">
						</div>
					</div>
					<div class="col-sm-6">
						<div class="input-group">
							<div class="input-group-prepend">
								<label class="input-group-text" for="here_appcode">
									<?= I18N::translate('App Code') ?>
								</label>
							</div>
							<input id="here_appcode" class="form-control" type="text"
								   name="here_appcode"
								   value="<?= $here_Appcode ?>">
						</div>
					</div>
				</div>
				<p class="small text-muted"><?= I18N::translate(
						'HERE WeGo&copy; requires that you register and obtain both an App ID and an App Code before the service can be used'
					) ?>
					<a href="https://developer.here.com/plans/api/consumer-mapping/"><?= I18N::translate('Get HERE WeGo codes') ?></a>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- SAVE BUTTON -->
	<div class="form-group row">
		<div class="offset-sm-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<i class="fas fa-check"></i>
				<?= I18N::translate('save') ?>
			</button>
		</div>
	</div>
</form>

<?php View::push('javascript') ?>

	<script>
		'use strict';

		let domSelect = $('#provider');
		$(function() {
			if($('#mapbox_id').val().length === 0 || !$('#mapbox_token').length === 0) {
				domSelect.children('option[value=\"mapbox\"]').attr('disabled', 'disabled');
			}
			if($('#here_appid').val().length === 0 || !$('#here_appcode').length === 0) {
				domSelect.children('option[value=\"here\"]').attr('disabled', 'disabled');
			}
		});
		domSelect.change(function () {
			let newProvider = this.value;
			$.getJSON('<?= e(route('module')) ?>', {
				module  : '<?= $module ?>',
				action  : 'ProviderStyles',
				provider: newProvider,
			})
				.done(function (data, textStatus, jqXHR) {
					let html = '';
					Object.keys(data).forEach(function(key) {
						html += '<option value=' + key +'>' + data[key] + '</option>';
					});
					$('#provider_style').html(html);
				})
				.fail(function (jqXHR, textStatus, errorThrown) {
					console.log(jqXHR, textStatus, errorThrown);
				})
		});
	</script>
<?php View::endpush() ?>

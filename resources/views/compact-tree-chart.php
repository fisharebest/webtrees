<table class="text-center w-100 h-100" role="presentation">
	<tr>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[16]]) ?>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td class="h-100">
			<?= view('compact-tree-individual', ['individual' => $ancestors[18]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[24]]) ?>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[26]]) ?>
		</td>
	</tr>
	<tr>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-up', 'individual' => $ancestors[16]]) ?>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-up', 'individual' => $ancestors[18]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-up', 'individual' => $ancestors[24]]) ?>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-up', 'individual' => $ancestors[26]]) ?>
		</td>
	</tr>
	<tr>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[8]]) ?>
		</td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-start', 'individual' => $ancestors[8]]) ?>
		</td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[4]]) ?>
		</td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-end', 'individual' => $ancestors[9]]) ?>
		</td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[9]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[12]]) ?>
		</td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-start', 'individual' => $ancestors[12]]) ?>
		</td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[6]]) ?>
		</td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-end', 'individual' => $ancestors[13]]) ?>
		</td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[13]]) ?>
		</td>
	</tr>
	<tr>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-down', 'individual' => $ancestors[17]]) ?>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-down', 'individual' => $ancestors[19]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-down', 'individual' => $ancestors[25]]) ?>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-down', 'individual' => $ancestors[27]]) ?>
		</td>
	</tr>
	<tr>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[17]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-up', 'individual' => $ancestors[4]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[19]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[25]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-up', 'individual' => $ancestors[6]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[27]]) ?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[2]]) ?>
		</td>
		<td></td>
		<td colspan="3">
			<table width="100%">
				<tr>
					<td width='25%'>
						<?= view('compact-tree-arrow', ['class' => 'arrow-start', 'individual' => $ancestors[2]]) ?>
					</td>
					<td>
						<?= view('compact-tree-individual', ['individual' => $ancestors[1]]) ?>
					</td>
					<td width='25%'>
						<?= view('compact-tree-arrow', ['class' => 'arrow-end', 'individual' => $ancestors[3]]) ?>
					</td>
				</tr>
			</table>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[3]]) ?>
		</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[20]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-down', 'individual' => $ancestors[5]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[22]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[28]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-down', 'individual' => $ancestors[7]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[30]]) ?>
		</td>
	</tr>
	<tr>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-up', 'individual' => $ancestors[20]]) ?>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-up', 'individual' => $ancestors[22]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-up', 'individual' => $ancestors[28]]) ?>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-up', 'individual' => $ancestors[30]]) ?>
		</td>
	</tr>
	<tr>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[10]]) ?>
		</td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-start', 'individual' => $ancestors[10]]) ?>
		</td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[5]]) ?>
		</td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-end', 'individual' => $ancestors[11]]) ?>
		</td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[11]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[14]]) ?>
		</td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-start', 'individual' => $ancestors[14]]) ?>
		</td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[7]]) ?>
		</td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-end', 'individual' => $ancestors[15]]) ?>
		</td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[15]]) ?>
		</td>
	</tr>
	<tr>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-down', 'individual' => $ancestors[21]]) ?>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-down', 'individual' => $ancestors[23]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-down', 'individual' => $ancestors[29]]) ?>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td>
			<?= view('compact-tree-arrow', ['class' => 'arrow-down', 'individual' => $ancestors[31]]) ?>
		</td>
	</tr>
	<tr>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[21]]) ?>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[23]]) ?>
		</td>
		<td></td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[29]]) ?>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td>
			<?= view('compact-tree-individual', ['individual' => $ancestors[31]]) ?>
		</td>
	</tr>
</table>

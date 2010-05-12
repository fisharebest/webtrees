gedcom_stats_block|gedcom_stats_descr
<div class="gedcom_stats">
<span style="font-weight: bold"><a href="index.php?command=gedcom">#gedcomTitle#</a></span><br />
#pgv_lang[gedcom_created_using]##pgv_lang[gedcom_created_on2]#<br />
<table>
	<tr>
		<td valign="top" class="width20">
			<table cellspacing="1" cellpadding="0">
				<tr>
					<td class="facts_label">#stat_individuals# </td>
					<td class="facts_value">&nbsp;<a href="indilist.php?surname_sublist=no">#totalIndividuals#</a></td>
				</tr>
				<tr>
					<td class="facts_label">#stat_surnames# </td>
					<td class="facts_value">&nbsp;<a href="indilist.php?surname_sublist=yes">#totalSurnames#</a></td>
				</tr>
				<tr>
					<td class="facts_label">#stat_families# </td>
					<td class="facts_value">&nbsp;<a href="famlist.php">#totalFamilies#</a></td>
				</tr>
				<tr>
					<td class="facts_label">#stat_sources# </td>
					<td class="facts_value">&nbsp;<a href="sourcelist.php">#totalSources#</a></td>
				</tr>
				<tr>
					<td class="facts_label">#stat_media# </td>
					<td class="facts_value">&nbsp;#totalMedia#</td>
				</tr>
				<tr>
					<td class="facts_label">#stat_other# </td>
					<td class="facts_value">&nbsp;#totalOtherRecords#</td>
				</tr>
				<tr>
					<td class="facts_label">#stat_events# </td>
					<td class="facts_value">&nbsp;#totalEvents#</td>
				</tr>
				<tr>
					<td class="facts_label">#stat_males# </td>
					<td class="facts_value">&nbsp;#totalSexMales# [#totalSexMalesPercentage#%]</td>
				</tr>
				<tr>
					<td class="facts_label">#stat_females# </td>
					<td class="facts_value">&nbsp;#totalSexFemales#  [#totalSexFemalesPercentage#%]</td>
				</tr>
				<tr>
					<td class="facts_label">#stat_users# </td>
					<td class="facts_value">&nbsp;#totalUsers#</td>
				</tr>
			</table>
		</td>
		<td><br /></td>
		<td valign="top">
			<table cellspacing="1" cellpadding="0" border="0">
				<tr>
					<td class="facts_label">#stat_earliest_birth#</td>
					<td class="facts_value">&nbsp;#firstBirthYear#&nbsp;</td>
					<td class="facts_value">#firstBirth#</td>
				</tr>
				<tr>
					<td class="facts_label">#stat_latest_birth#</td>
					<td class="facts_value">&nbsp;#lastBirthYear#&nbsp;</td>
					<td class="facts_value">#lastBirth#</td>
				</tr>
				<tr>
					<td class="facts_label">#stat_earliest_death#</td>
					<td class="facts_value">&nbsp;#firstDeathYear#&nbsp;</td>
					<td class="facts_value">#firstDeath#</td>
				</tr>
				<tr>
					<td class="facts_label">#stat_latest_death#</td>
					<td class="facts_value">&nbsp;#lastDeathYear#&nbsp;</td>
					<td class="facts_value">#lastDeath#</td>
				</tr>
				<tr>
					<td class="facts_label">#stat_longest_life#</td>
					<td class="facts_value">&nbsp;#longestLifeAge#&nbsp;</td>
					<td class="facts_value">#longestLife#</td>
				</tr>
				<tr>
					<td class="facts_label">#stat_avg_age_at_death#</td>
					<td class="facts_value">&nbsp;#averageLifespan#&nbsp;</td>
					<td class="facts_value"></td>
				</tr>
				<tr>
					<td class="facts_label">#stat_most_children#</td>
					<td class="facts_value">&nbsp;#largestFamilySize#&nbsp;</td>
					<td class="facts_value">#largestFamily#</td>
				</tr>
				<tr>
					<td class="facts_label">#stat_average_children#</td>
					<td class="facts_value">&nbsp;#averageChildren#</td>
					<td class="facts_value"></td>
				</tr>
			</table>
		</td>
	</tr>
</table><br />
#help:index_common_names_help#<span style="font-weight: bold">#lang:common_surnames#</span><br />
#commonSurnames#
</div>

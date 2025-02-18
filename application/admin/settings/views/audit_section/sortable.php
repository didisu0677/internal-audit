<ol class="sortable">
	<?php foreach($audit_section[0] as $m0) { ?>
	<li id="mst_accountItem_<?php echo $m0->id; ?>" class="module" data-module="<?php echo $m0->section_code; ?>">
		<div class="sort-item">
			<span class="item-title"><?php echo $m0->section_name; ?></span>
		</div>
		<?php if(isset($audit_section[$m0->id]) && count($audit_section[$m0->id]) > 0) { ?>
		<ol>
			<?php foreach($audit_section[$m0->id] as $m1) { ?>
			<li id="mst_accountItem_<?php echo $m1->id; ?>" data-module="<?php echo $m0->section_code; ?>">
				<div class="sort-item">
					<span class="item-title"><?php echo $m1->section_name; ?></span>
				</div>
				<?php if(isset($audit_section[$m1->id]) && count($audit_section[$m1->id]) > 0) { ?>
				<ol>
					<?php foreach($audit_section[$m1->id] as $m2) { ?>
					<li id="mst_accountItem_<?php echo $m2->id; ?>" data-module="<?php echo $m0->section_code; ?>">
						<div class="sort-item">
							<span class="item-title"><?php echo $m2->section_name; ?></span>
						</div>
						<?php if(isset($audit_section[$m2->id]) && count($audit_section[$m2->id]) > 0) { ?>
						<ol>
							<?php foreach($audit_section[$m2->id] as $m3) { ?>
							<li id="mst_accountItem_<?php echo $m3->id; ?>" data-module="<?php echo $m0->section_code; ?>">
								<div class="sort-item">
									<span class="item-title"><?php echo $m3->section_name; ?></span>
								</div>
							</li>
							<?php } ?>
						</ol>
						<?php } ?>
					</li>
					<?php } ?>
				</ol>
				<?php } ?>
			</li>
			<?php } ?>
		</ol>
		<?php } ?>
	</li>
	<?php } ?>
</ol>
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	template : `
<div class="text-center">
	<span class="d-inline-block overflow-hidden">
    <v-progress-circular
      :size="50"
      color="primary"
      indeterminate
    ></v-progress-circular>
	</span>
</div>
	`,
}

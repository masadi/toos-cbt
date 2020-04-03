<div class="modal-dialog <?php echo isset($modal_s) ? $modal_s : 'modal-lg'; ?>">
	<div class="modal-content panel-warning">
		<div class="modal-header">
      <h4 class="modal-title">@yield('title')</h4>
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		</div>
		<div class="modal-body">
			@yield('content')
		</div>
		
		<div class="modal-footer">
			@yield('footer')
		</div>
		
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
@yield('js')
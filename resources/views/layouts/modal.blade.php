<div class="modal-dialog <?php echo isset($modal_s) ? $modal_s : 'modal-lg'; ?>">
	<div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title">@yield('title')</h4>
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			@yield('content')
		</div>
		<div class="modal-footer justify-content-between">
			@yield('footer')
		</div>
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
@include('adminlte::plugins', ['type' => 'css'])
@include('adminlte::plugins', ['type' => 'js'])
@yield('js')

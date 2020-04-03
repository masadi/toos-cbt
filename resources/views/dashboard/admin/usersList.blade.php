@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="animated fadeIn">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
        <div class="card">
          <div class="card-header">
            <i class="fa fa-align-justify"></i>{{ __('Users') }}</div>
          <div class="card-body">
            @if (session('success'))
            <div class="alert alert-success" role="alert">
              {{ session('success') }}
            </div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger" role="alert">
              {{ session('error') }}
            </div>
            @endif
            @if ($errors->any())
            <div class="alert alert-danger" role="alert">
              @foreach ($errors->all() as $error)
              {{ $error }}<br />
              @endforeach
            </div>
            @endif
            <table id="datatable" class="table table-responsive-sm table-striped">
              <thead>
                <tr>
                  <th>Username</th>
                  <th>E-mail</th>
                  <th>Roles</th>
                  <th>Email verified at</th>
                  <th>View</th>
                  <th>Edit</th>
                  <th>Delete</th>
                </tr>
              </thead>
              <tbody>
                {{--
                          @foreach($users as $user)
                            <tr>
                              <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->menuroles }}</td>
                <td>{{ $user->email_verified_at }}</td>
                <td>
                  <a href="{{ url('/users/' . $user->user_id) }}" class="btn btn-block btn-primary">View</a>
                </td>
                <td>
                  <a href="{{ url('/users/' . $user->user_id . '/edit') }}" class="btn btn-block btn-primary">Edit</a>
                </td>
                <td>
                  @if( $you->user_id !== $user->user_id )
                  <form action="{{ route('users.destroy', $user->user_id ) }}" method="POST">
                    @method('DELETE')
                    @csrf
                    <button class="btn btn-block btn-danger">Delete User</button>
                  </form>
                  @endif
                </td>
                </tr>
                @endforeach
                --}}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('assets/dataTables/jquery.dataTables.min.css') }}">
@endsection
@section('javascript')
<script src="{{ asset('assets/dataTables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script>
  $(function() {
    var table = null;
    function init() {
        table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{route('ajax.get_all_data', ['query' => 'users'])}}',
            columns: [
                { data: 'username', name: 'username' },
                { data: 'email', name: 'email' },
                { data: 'menuroles', name: 'menuroles' },
                { data: 'email_verified_at', name: 'email_verified_at' },
                { data: 'view', name: 'view', orderable: false, searchable: false },
                { data: 'edit', name: 'edit', orderable: false, searchable: false },
                { data: 'delete', name: 'delete', orderable: false, searchable: false }
            ],
            fnDrawCallback: function(oSettings) {
                turn_on_icheck();
            }
        });
    }
    init();
    function turn_on_icheck() {
        $('a.toggle-modal').bind('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            if (url.indexOf('#') == 0) {
                $('#modal_content').modal('open');
            } else {
                $.get(url, function(data) {
                    $('#modal_content').modal();
                    $('#modal_content').html(data);
                });
            }
        });
        $('a.toggle-swal').bind('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            Swal.fire({
                title: "Anda Yakin?",
                text: "Tindakan ini tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Generate!'
            }).then((result) => {
                if (result) {
                    console.log(result);
                    if (result.value) {
                        $.get(url, function(data) {
                            Swal.fire({
                                icon: data.icon,
                                text: data.status,
                            }).then(function(e) {
                                table.ajax.reload( null, false );
                            });
                        });
                    }
                }
            });
        });
        $('a.toggle-delete').bind('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            Swal.fire({
                title: "Anda Yakin?",
                text: "Tindakan ini tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result) {
                    console.log(result);
                    if (result.value) {
                        $.get(url, function(data) {
                            Swal.fire({
                                icon: data.icon,
                                text: data.status,
                            }).then(function(e) {
                                table.ajax.reload( null, false );
                            });
                        });
                    }
                }
            });
        });
    }
});
</script>
@endsection
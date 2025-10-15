@extends('layouts/layoutMaster')

@section('title', 'Roles - Edit')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            $('#selectAll').click(function() {
                const checked = this.checked;
                $('.checkbox').each(function() {
                    this.checked = checked;
                });
            });
        });
    </script>
@endsection

@section('content')
    <!-- Add Role Modal -->
    <div class="row">
        <div class="col-xxl">
            <!-- <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role"> -->
            <!-- <div class="modal-content p-3 p-md-5"> -->
            <div class="card mb-4">
                <!-- <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button> -->
                <!-- <div class="modal-body"> -->
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3 class="role-title mb-2">{{ _t('Edit') }}</h3>
                    </div>
                    <!-- Add role form -->
                    <form id="addRoleForm" class="row g-3" method="post" action="{{ route('roles.update', $role->id) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $role->id }}">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label" for="modalRoleName">{{ _t('Role name') }}
                                    *</label>
                                <input type="text" id="modalRoleName" name="name" value="{{ $role->name }}"
                                    class="form-control" placeholder="{{ _t('Role name') }}" required />
                                @error('name')
                                    <span class="text-danger">{{ _t($message) }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <h5>{{ _t('Role permissions') }}</h5>
                            <!-- Permission table -->
                            <div class="table-responsive">
                                <table class="table table-flush-spacing">
                                    <tbody>
                                        <tr>
                                            <td class="text-nowrap fw-medium">{{ _t('Admin') }} <i
                                                    class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="{{ _t('Allow full access') }}"></i></td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="selectAll" />
                                                    <label class="form-check-label" for="selectAll">
                                                        {{ _t('Select all') }}
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        @foreach ($permissions as $group => $permission)
                                            <tr>
                                                <td class="text-nowrap fw-medium">{{ _t($group) }}</td>
                                                @foreach ($permission as $perm)
                                                    <td>
                                                        <div class="d-flex">
                                                            <div class="form-check me-3 me-lg-5">
                                                                <input class="form-check-input checkbox" type="checkbox"
                                                                    id="userManagementRead" value="{{ $perm->id }}"
                                                                    name="permissions[]"
                                                                    {{ in_array($perm->id, $rolePermissions) ? 'checked' : null }} />
                                                                <label class="form-check-label" for="userManagementRead">
                                                                    {{ _t(str_replace('_', ' ', $perm->name)) }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- Permission table -->
                        </div>
                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-primary">{{ _t('Edit') }}</button>
                        </div>
                    </form>
                    <!--/ Add role form -->
                </div>
            </div>
        </div>
    </div>
    <!--/ Add Role Modal -->
@endsection

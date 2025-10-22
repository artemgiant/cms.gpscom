<div class="modal fade" id="deleteUserModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Удалить пользователя</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form action="{{ route('user.destroy') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('DELETE')
                <input id="deleteUserId" type="hidden" name="user_id">

                <div class="modal-body">

                    <div class="row">
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller"
                                           class="col-sm-12 col-form-label font-weight-bold">Передать права:</label>
                                    <div class="col-sm-12">
                                        <select id="usersList" class="form-control" name="new_user_id" data-size="7" data-live-search="true" required>
                                            <option value="" selected>Пользователь</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->getFullName() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-lg btn-light-primary font-weight-bold" data-dismiss="modal">
                        Закрыть
                    </button>
                    <button type="submit" class="btn btn-lg btn-primary mr-2">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>

@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.fkilkom.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.fkilkoms.update", [$fkilkom->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="nidn">{{ trans('cruds.fkilkom.fields.nidn') }}</label>
                <input class="form-control {{ $errors->has('nidn') ? 'is-invalid' : '' }}" type="text" name="nidn" id="nidn" value="{{ old('nidn', $fkilkom->nidn) }}" required>
                @if($errors->has('nidn'))
                    <div class="invalid-feedback">
                        {{ $errors->first('nidn') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.fkilkom.fields.nidn_helper') }}</span>
            </div>
            <div class="form-group">
              <label class="required" for="nama_dosen">{{ trans('cruds.fkilkom.fields.nama_dosen') }}</label>
              <input class="form-control {{ $errors->has('nama_dosen') ? 'is-invalid' : '' }}" type="text" name="nama_dosen" id="nama_dosen" value="{{ old('nama_dosen', $fkilkom->nama_dosen) }}" required>
              @if($errors->has('nama_dosen'))
                  <div class="invalid-feedback">
                      {{ $errors->first('nama_dosen') }}
                  </div>
              @endif
              <span class="help-block">{{ trans('') }}</span>
          </div>
          <div class="form-group">
            <label class="required" for="kode_matakuliah">{{ trans('cruds.fkilkom.fields.kode_matakuliah') }}</label>
            <input class="form-control {{ $errors->has('kode_matakuliah') ? 'is-invalid' : '' }}" type="text" name="kode_matakuliah" id="kode_matakuliah" value="{{ old('kode_matakuliah', $fkilkom->kode_matakuliah) }}" required>
            @if($errors->has('kode_matakuliah'))
                <div class="invalid-feedback">
                    {{ $errors->first('kode_matakuliah') }}
                </div>
            @endif
            <span class="help-block">{{ trans('') }}</span>
        </div>
        <div class="form-group">
          <label class="required" for="matakuliah">{{ trans('cruds.fkilkom.fields.matakuliah') }}</label>
          <input class="form-control {{ $errors->has('matakuliah') ? 'is-invalid' : '' }}" type="text" name="matakuliah" id="matakuliah" value="{{ old('matakuliah', $fkilkom->matakuliah) }}" required>
          @if($errors->has('matakuliah'))
              <div class="invalid-feedback">
                  {{ $errors->first('matakuliah') }}
              </div>
          @endif
          <span class="help-block">{{ trans('') }}</span>
      </div>
            <div class="form-group">
              <label for="sks">{{ trans('cruds.fkilkom.fields.sks') }}</label>
              <input class="form-control {{ $errors->has('sks') ? 'is-invalid' : '' }}" type="number" name="sks" id="sks" value="{{ old('sks', $fkilkom->sks) }}" step="0.01">
              @if($errors->has('sks'))
                  <div class="invalid-feedback">
                      {{ $errors->first('sks') }}
                  </div>
              @endif
              <span class="help-block">{{ trans('') }}</span>
          </div>
          <div class="form-group">
            <label>{{ trans('Select Jurusan') }}</label>
            <select class="form-control {{ $errors->has('jurusan') ? 'is-invalid' : '' }}" name="jurusan" id="jurusan">
                <option value disabled {{ old('jurusan', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                @foreach(App\Models\Fkilkom::JURUSAN_SELECT as $key => $label)
                    <option value="{{ $key }}" {{ old('jurusan', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @if($errors->has('jurusan'))
                <div class="invalid-feedback">
                    {{ $errors->first('jurusan') }}
                </div>
            @endif
            <span class="help-block">{{ trans('') }}</span>
        </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
<script>
    $(document).ready(function () {
  function SimpleUploadAdapter(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
      return {
        upload: function() {
          return loader.file
            .then(function (file) {
              return new Promise(function(resolve, reject) {
                // Init request
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('admin.fkilkoms.storeCKEditorImages') }}', true);
                xhr.setRequestHeader('x-csrf-token', window._token);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.responseType = 'json';

                // Init listeners
                var genericErrorText = `Couldn't upload file: ${ file.name }.`;
                xhr.addEventListener('error', function() { reject(genericErrorText) });
                xhr.addEventListener('abort', function() { reject() });
                xhr.addEventListener('load', function() {
                  var response = xhr.response;

                  if (!response || xhr.status !== 201) {
                    return reject(response && response.message ? `${genericErrorText}\n${xhr.status} ${response.message}` : `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`);
                  }

                  $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');

                  resolve({ default: response.url });
                });

                if (xhr.upload) {
                  xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                      loader.uploadTotal = e.total;
                      loader.uploaded = e.loaded;
                    }
                  });
                }

                // Send request
                var data = new FormData();
                data.append('upload', file);
                data.append('crud_id', '{{ $fkilkom->id ?? 0 }}');
                xhr.send(data);
              });
            })
        }
      };
    }
  }

  var allEditors = document.querySelectorAll('.ckeditor');
  for (var i = 0; i < allEditors.length; ++i) {
    ClassicEditor.create(
      allEditors[i], {
        extraPlugins: [SimpleUploadAdapter]
      }
    );
  }
});
</script>

<script>
    Dropzone.options.imageDropzone = {
    url: '{{ route('admin.fkilkoms.storeMedia') }}',
    maxFilesize: 2, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="image"]').remove()
      $('form').append('<input type="hidden" name="image" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="image"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($fkilkom) && $fkilkom->image)
      var file = {!! json_encode($fkilkom->image) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="image" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
    error: function (file, response) {
        if ($.type(response) === 'string') {
            var message = response //dropzone sends it's own error messages in string
        } else {
            var message = response.errors.file
        }
        file.previewElement.classList.add('dz-error')
        _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
        _results = []
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            node = _ref[_i]
            _results.push(node.textContent = message)
        }

        return _results
    }
}

</script>
@endsection
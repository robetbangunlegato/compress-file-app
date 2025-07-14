<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>

<body>
    <style>
        /* css untuk menghilangkan panah atas bawah ketika type input number */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>

    <div class="contianer">
        <div class="d-grid justify-content-center align-items-center"
            style="height: 100vh; background-color: antiquewhite">
            <div>
                <h1 style="">Hello 👋, welcome to compress file App</h1>
                <p class="text-center bg-success rounded-pill h3 p-2 text-white">Put you're file in below 👇</p>
                <form action="{{ route('file.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mt-5">
                        <label for="formFile" class="form-label">Upload file mu disini (JPG/PDF) (Max.10MB)</label>
                        <input class="form-control @error('formFile') is-invalid @enderror" type="file"
                            id="formFile" name="formFile" required>
                        @error('formFile')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mt-3">
                        <label for="formSize" class="form-label">Ukuran maksimum (dalam kilobyte/KB) (Min.50KB)
                            (Max.1000KB)</label>
                        <input class="form-control @error('formSize') is-invalid @enderror" type="number"
                            min="50" max="1000" placeholder="Masukan angka saja dalam kilobyte, contoh : 100"
                            aria-label="default input example" id="formSize" name="formSize" required>
                        @error('formSize')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mt-5 text-center">
                        <button class="btn btn-primary w-100" type="submit">Compress</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous">
    </script>
</body>

</html>

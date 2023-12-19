<div id="image-canvas-wrapper" style="position: relative; width: 1px;height: 1px; overflow: hidden; opacity: 0;">

</div>
<script type="module">
    import {
        Compiler
    } from "{{ asset('static/plugins/mindar/mindar-image.prod.js') }}";

    Dropzone.autoDiscover = false;
    //document.getElementById('mindar-module')

    let maxWidth = 0;

    let imageCanvas = null;

    const compiler = new Compiler();

    const readFileData = (blob, callback) => {

        var reader = new FileReader();
        reader.readAsDataURL(blob);
        reader.onloadend = function() {
            var base64String = reader.result;
            callback(base64String);

            // console.log('Base64 String - ', base64String);

            // // Simply Print the Base64 Encoded String,
            // // without additional data: Attributes.
            // console.log('Base64 String without Tags- ',
            //     base64String.substr(base64String.indexOf(', ') + 1));
        }
    }
    const download = (buffer) => {
        var blob = new Blob([buffer]);
        // readFileData(blob);
        var aLink = window.document.createElement('a');
        aLink.download = 'targets.mind';
        aLink.href = window.URL.createObjectURL(blob);
        aLink.click();
        window.URL.revokeObjectURL(aLink.href);
    }

    const saveMind = (buffer) => {
        var blob = new Blob([buffer]);
        readFileData(blob, data => document.getElementById('nft_file_data').value = data);
    }

    const insertAfter = (referenceNode, newNode) => referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);


    const getProgressElement = () => {
        if (document.getElementById('compile-progress-bar'))
            return document.getElementById('compile-progress-bar');
        let customFileWrapper = document.querySelector('#tracking_image_file-form-group .custom-file');
        let progressBarElement = document.createElement('div');
        progressBarElement.id = 'compile-progress-bar';
        progressBarElement.classList.add('mt-1', 'mb-2', 'text-info');
        insertAfter(customFileWrapper, progressBarElement);
        return progressBarElement
    }

    const showProgress = percent => {
        let progress = getProgressElement();
        if (parseInt(percent) == 100)
            progress.innerHTML = '';
        else
            progress.innerHTML = 'Đang xử lý... ' + percent + '%';
    }

    const showData = (data) => {
        // console.log("data", data);
        for (let i = 0; i < data.trackingImageList.length; i++) {
            const image = data.trackingImageList[i];

            const points = data.trackingData[i].points.map((p) => {
                return {
                    x: Math.round(p.x),
                    y: Math.round(p.y)
                };
            });
            showImage(image, points);
        }

        // for (let i = 0; i < data.imageList.length; i++) {
        //     const image = data.imageList[i];
        //     const kpmPoints = [...data.matchingData[i].maximaPoints, ...data.matchingData[i].minimaPoints];
        //     const points2 = [];
        //     for (let j = 0; j < kpmPoints.length; j++) {
        //         points2.push({
        //             x: Math.round(kpmPoints[j].x),
        //             y: Math.round(kpmPoints[j].y)
        //         });
        //     }
        //     showImage(image, points2);
        // }
    }


    const showImage = (targetImage, points) => {
        if(targetImage.width <  maxWidth){
            return;
        }
        const container = document.getElementById("image-canvas-wrapper");
        container.innerHTML = '';
        const canvas = document.createElement('canvas');
        container.appendChild(canvas);
        maxWidth = targetImage.width;

        canvas.width = targetImage.width;
        canvas.height = targetImage.height;
        canvas.style.width = canvas.width;
        const ctx = canvas.getContext('2d');
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = new Uint32Array(imageData.data.buffer);

        const alpha = (0xff << 24);
        for (let c = 0; c < targetImage.width; c++) {
            for (let r = 0; r < targetImage.height; r++) {
                const pix = targetImage.data[r * targetImage.width + c];
                data[r * canvas.width + c] = alpha | (pix << 16) | (pix << 8) | pix;
            }
        }

        var pix = (0xff << 24) | (0x00 << 16) | (0xff << 8) | 0x00; // green
        for (let i = 0; i < points.length; ++i) {
            const x = points[i].x;
            const y = points[i].y;
            const offset = (x + y * canvas.width);
            data[offset] = pix;
            //for (var size = 1; size <= 3; size++) {
            for (var size = 1; size <= 6; size++) {
                data[offset - size] = pix;
                data[offset + size] = pix;
                data[offset - size * canvas.width] = pix;
                data[offset + size * canvas.width] = pix;
            }
        }
        ctx.putImageData(imageData, 0, 0);
        imageCanvas = canvas.toDataURL();
    }

    const loadImage = async (file) => {
        const img = new Image();

        return new Promise((resolve, reject) => {
            let img = new Image()
            img.onload = () => resolve(img);
            img.onerror = reject;
            img.src = URL.createObjectURL(file);
            //img.src = src
        })
    }

    const compileFiles = async (files) => {
        // App.Swal.showLoading();
        maxWidth = 0;
        imageCanvas = null;
        document.getElementById('tracking_image_file').style.display = 'none';
        const images = [];
        for (let i = 0; i < files.length; i++) {
            images.push(await loadImage(files[i]));
        }
        let _start = new Date().getTime();
        const dataList = await compiler.compileImageTargets(images, (progress) => {
            // document.getElementById("progress").innerHTML = 'progress: ' + progress.toFixed(2) + "%";
            showProgress(progress.toFixed(2));
        });
        // console.log('exec time compile: ', new Date().getTime() - _start);
        for (let i = 0; i < dataList.length; i++) {
            showData(dataList[i]);
        }
        const exportedBuffer = await compiler.exportData();
        saveMind(exportedBuffer);

        document.getElementById('tracking_image_file').style.display = 'block';
        // console.log(imageCanvas);
        if (imageCanvas) {
            let progressBarElement = getProgressElement();

            progressBarElement.innerHTML = '';
            let img = document.createElement('img');
            img.style.maxWidth = '200px';
            img.src = imageCanvas;
            progressBarElement.appendChild(img);
            // img.onerror = reject;

        }
        // App.Swal.hideLoading();

        // document.getElementById("downloadButton").addEventListener("click", function () {
        //     download(exportedBuffer);
        // });


    }

    const loadMindFile = async (file) => {
        var reader = new FileReader();
        reader.onload = function() {
            const dataList = compiler.importData(this.result);
            for (let i = 0; i < dataList.length; i++) {
                showData(dataList[i]);
            }
        }
        reader.readAsArrayBuffer(file);
    }

    document.addEventListener('DOMContentLoaded', function(event) {
        let trackingInputFile = document.getElementById('tracking_image_file');
        trackingInputFile.addEventListener('change', event => {
            const files = trackingInputFile.files;
            if (files.length === 0) return;
            const ext = files[0].name.split('.').pop();
            if (ext === 'mind') {
                loadMindFile(files[0]);
            } else {
                compileFiles(files);
            }
        });

        // const myDropzone = new Dropzone("#dropzone", { url: "#", autoProcessQueue: false, addRemoveLinks: true });
        // myDropzone.on("addedfile", function (file) { });

        // document.getElementById("startButton").addEventListener("click", function () {
        //     const files = myDropzone.files;
        //     if (files.length === 0) return;
        //     const ext = files[0].name.split('.').pop();
        //     if (ext === 'mind') {
        //         loadMindFile(files[0]);
        //     } else {
        //         compileFiles(files);
        //     }
        // });
    });
    //};
</script>

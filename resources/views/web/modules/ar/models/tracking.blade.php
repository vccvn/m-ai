@section('title', $model->name)
<!DOCTYPE html>
<html>

<head>
    @include($_lib . 'meta')
    @include($_lib . 'head')
    @if ($model->lib == 'mind')
    <script src="https://aframe.io/releases/1.4.2/aframe.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/donmccurdy/aframe-extras@v6.1.1/dist/aframe-extras.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mind-ar@1.2.2/dist/mindar-image-aframe.prod.js"></script>
    @else
        <!-- import aframe and then ar.js with image tracking / location based features -->
        <script src="https://cdn.jsdelivr.net/gh/aframevr/aframe@1.3.0/dist/aframe-master.min.js"></script>
        <script src="https://raw.githack.com/AR-js-org/AR.js/master/aframe/build/aframe-ar-nft.js"></script>

        <!-- style for the loader -->
        <style>
            .arjs-loader {
                height: 100%;
                width: 100%;
                position: absolute;
                top: 0;
                left: 0;
                background-color: rgba(0, 0, 0, 0.8);
                z-index: 9999;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .arjs-loader div {
                text-align: center;
                font-size: 1.25em;
                color: white;
            }
        </style>

    @endif
</head>

<body style="margin : 0px; overflow: hidden;">
    @if ($model->lib == 'mind')
        {{-- <a-scene mindar-image="imageTargetSrc: {{ $model->getTrackingNFTUrl() }}" color-space="sRGB" renderer="colorManagement: true, physicallyCorrectLights" vr-mode-ui="enabled: false" device-orientation-permission-ui="enabled: false">
            <a-assets>
        <img id="card" src="{{ $model->getTrackingImageUrl() }}" />
        <a-asset-item id="avatarModel" src="{{ $model->getModelFileUrl() }}"></a-asset-item>
            </a-assets>

            <a-camera position="0 0 0" look-controls="enabled: false"></a-camera>

            <a-entity mindar-image-target="targetIndex: 0">
            <a-plane src="#card" position="0 0 0" height="0.552" width="1" rotation="0 0 0"></a-plane>
            <a-gltf-model rotation="0 0 0 " position="0 0 0.1" scale="0.005 0.005 0.005" src="#avatarModel"
                animation="property: position; to: 0 0.1 0.1; dur: 1000; easing: easeInOutQuad; loop: true; dir: alternate"
            >
            </a-entity>
        </a-scene> --}}


        <a-scene mindar-image="imageTargetSrc: {{ $model->getTrackingNFTUrl() }}; maxTrack: 1" color-space="sRGB" renderer="colorManagement: true, physicallyCorrectLights" vr-mode-ui="enabled: false" device-orientation-permission-ui="enabled: false">
            <a-assets>
              <a-asset-item id="bearModel" src="{{ $model->getModelFileUrl() }}"></a-asset-item>
              {{-- <a-asset-item id="raccoonModel" src="https://cdn.jsdelivr.net/gh/hiukim/mind-ar-js@1.2.2/examples/image-tracking/assets/band-example/raccoon/scene.gltf"></a-asset-item> --}}
            </a-assets>

            <a-camera position="0 0 0" look-controls="enabled: false"></a-camera>

            <a-entity mindar-image-target="targetIndex: 0">
              <a-gltf-model rotation="0 0 0 " position="0 0 0" scale="0.5 0.5 0.5" src="#bearModel" animation-mixer>
            </a-entity>
          </a-scene>
    @else
        <!-- minimal loader shown until image descriptors are loaded. Loading may take a while according to the device computational power -->
        <div class="arjs-loader">
            <div>Đang tải... <br>Vui lòng chờ giây lát</div>
        </div>



        <a-scene
            vr-mode-ui="enabled: false;"
            renderer="logarithmicDepthBuffer: true; precision: medium;"
            embedded
            arjs="trackingMethod: best; sourceType: webcam;debugUIEnabled: false;"
        >
        <!-- we use cors proxy to avoid cross-origin problems ATTENTION! you need to set up your server -->
            <a-nft
                type="nft"
                url="{{ $model->getTrackingNFTUrl() }}"
                smooth="true"
                smoothCount="10"
                smoothTolerance=".01"
                smoothThreshold="5"
            >
                <a-entity
                    gltf-model="{{ $model->getModelFileUrl() }}"
                    scale="50 50 50"
                    position="0 0 0"

                    rotation="-90 0 0"

                >
                </a-entity>
            </a-nft>
            <a-entity camera></a-entity>
        </a-scene>

    @endif
</body>

</html>

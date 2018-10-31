<!DOCTYPE html>
<html>
<head>
<title>System vis</title>

<link href="https://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:300,400" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="lib/style/style.css">


</head>
<body>

<h1>System visualisation</h1>
<p></p>


<script src="lib/js/three.js"></script>
<script src="lib/js/OrbitControls.js"></script>
<script src="lib/js/libs/stats.min.js"></script>
<script src="lib/js/libs/dat.gui.min.js"></script>
<script src="lib/js/OrbitControls.js"></script>
<script src="lib/js/loaders/GLTFLoader.js"></script>

<script src="lib/js/postprocessing/EffectComposer.js"></script>
<script src="lib/js/postprocessing/RenderPass.js"></script>
<script src="lib/js/postprocessing/ShaderPass.js"></script>
<script src="lib/js/shaders/CopyShader.js"></script>
<script src="lib/js/shaders/LuminosityHighPassShader.js"></script>
<script src="lib/js/postprocessing/UnrealBloomPass.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<div id="container"></div>


<script>
    
    // GET DATA FROM PHP VIA AJAX
    
    var sysida, position, sunid, planetid, sunclass, sunname, suntemp, sunmass, sunradius, sunlumi, sunhex; 
    var jsonObj;
   


$.ajax({
    //type: "GET",
    url: 'system_vis_data.php',
    dataType: 'JSON',
    async: true,
    success: function(data){
        console.log(data);
        jsonObj = data;
      }

});





setTimeout(func, 1500);  // needed for proper launch of all AJAX vars
function func() {
    console.log('timeout');
    //console.log(jsonObj);

    // DRAWING 3D OBJECTS
			
            var scene, camera, controls, pointLight, stats;
			var composer, renderer, mixer;
			
            var params = {
				exposure: 1,
				bloomStrength: 3,
				bloomThreshold: 0,
				bloomRadius: 0
			};
			
            var clock = new THREE.Clock();
			var container = document.getElementById( 'container' );
			
            //stats = new Stats();
			//container.appendChild( stats.dom );
			
            renderer = new THREE.WebGLRenderer( { antialias: true } );
			renderer.setPixelRatio( window.devicePixelRatio );
			renderer.setSize( window.innerWidth, window.innerHeight );
			renderer.toneMapping = THREE.ReinhardToneMapping;
			container.appendChild( renderer.domElement );
			
            scene = new THREE.Scene();
			
            camera = new THREE.PerspectiveCamera( 40, window.innerWidth / window.innerHeight, 1, 500 );
			camera.position.set( - 5, 2.5, - 305 );
			scene.add( camera );
			
            controls = new THREE.OrbitControls( camera, renderer.domElement );
			controls.maxPolarAngle = Math.PI * 0.5;
			controls.minDistance = 1;
			controls.maxDistance = 350;
			
            scene.add( new THREE.AmbientLight( 0x404040 ) );
			
            pointLight = new THREE.PointLight( 0xffffff, 1 );
			camera.add( pointLight );
			
            var renderScene = new THREE.RenderPass( scene, camera );
			
            var bloomPass = new THREE.UnrealBloomPass( new THREE.Vector2( window.innerWidth, window.innerHeight ), 1.5, 0.4, 0.85 );
			bloomPass.renderToScreen = true;
			bloomPass.threshold = params.bloomThreshold;
			bloomPass.strength = params.bloomStrength;
			bloomPass.radius = params.bloomRadius;
			
            composer = new THREE.EffectComposer( renderer );
			composer.setSize( window.innerWidth, window.innerHeight );
			composer.addPass( renderScene );
			composer.addPass( bloomPass );
            
            //test = jsonObj[0].Sun_Radius;
            //console.log(test);

            // Set up the sphere vars
                var radius = 40;
                var SEGMENTS = 16;
                var RINGS = 16;
                var distance = 400;

                spheres = new THREE.Object3D();

                // create the sphere's material
                var x = 0;
                console.log(jsonObj[0].Position_X);
                console.log(jsonObj[0].Position_Y);
                console.log(jsonObj[0].Position_Z);

                for (var i = 0; i < jsonObj.length; i++) {
                    
                    radius = jsonObj[x].Sun_Radius;
                    var sphere = new THREE.SphereGeometry(radius, SEGMENTS, RINGS);
                    suncolor = parseInt((jsonObj[x].Chromaticity_hex).slice(1),16);
                    //sunhex = sunhex.substring(1); //remove the # from  color
                    //sunhex = parseInt(sunhex.slice(1), 16);
                
                    var material = new THREE.MeshLambertMaterial(
                    {
                    color: suncolor
                    });

                                  
                    //creating the mesh and add primitive and material
                    var particle = new THREE.Mesh(sphere, material);
                    //randomly set position and scale
                    particle.position.x = jsonObj[x].Position_X;
                    particle.position.y = jsonObj[x].Position_Y;
                    particle.position.z = jsonObj[x].Position_Z;
                    //particle.rotation.y = Math.floor(Math.random() * 101);
                    //particle.scale.x = particle.scale.y = particle.scale.z = Math.random() * 12 + 5;
                    //add particle to the spheres group
                    spheres.add(particle);

                    x++;
                }

                //correct spheres position relative to the camera
                spheres.position.y = 0;
                spheres.position.x = 0;
                spheres.position.z = 0;
                spheres.rotation.y = 0;
                //add spheres to the scene
                scene.add(spheres);

    ///////////////////////////////////////////////////////////////////////////////////////////////			
    // I DONT KNOW WHY THIS CODE IS NEEDED BUT I CANT DELETE IT BECAUSE IT STOPS RENDERING THEN
    ///////////////////////////////////////////////////////////////////////////////////////////////
        
        new THREE.GLTFLoader().load( 'lib/models/gltf/SimpleSkinning.glb', function ( gltf ) {
				
            //scene.add( model );
            
            // Mesh contains self-intersecting semi-transparent faces, which display
            // z-fighting unless depthWrite is disabled.
            //var core = model.getObjectByName( 'geo1_HoloFillDark_0' );
            //core.material.depthWrite = false;                       
              
            var model = gltf.scene;
            mixer = new THREE.AnimationMixer( model );
                
            var clip = gltf.animations[ 0 ];
            mixer.clipAction( clip.optimize() ).play();
            animate();
            
            } );           			
           
		
        window.onresize = function () {
			var width = window.innerWidth;
			var height = window.innerHeight;
			camera.aspect = width / height;
			camera.updateProjectionMatrix();
			renderer.setSize( width, height );
			composer.setSize( width, height );
			};
			
    ///////////////////////////////////////////////////////////////////////////////////////////////			
    // I DONT KNOW WHY THIS CODE IS NEEDED BUT I CANT DELETE IT BECAUSE IT STOPS RENDERING THEN
    ///////////////////////////////////////////////////////////////////////////////////////////////
        
        function animate() {
			requestAnimationFrame( animate );
			const delta = clock.getDelta();
			mixer.update( delta );
			//stats.update();
			composer.render();
			}

    }
		</script>





</body>
</html>
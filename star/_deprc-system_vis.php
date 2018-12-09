<!DOCTYPE html>
<html>
<head>
<title>System vis</title>

<link href="https://fonts.googleapis.com/css?family=Orbitron:400,700" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="lib/style/style.css">


</head>
<body>

<p>SYSTEM VIS</p>
<p></p>


<script src="lib/js/three.js"></script>
<script src="lib/js/OrbitControls.js"></script>
<script src="lib/js/libs/stats.min.js"></script>
<script src="lib/js/libs/dat.gui.min.js"></script>
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

    // DRAWING 3D OBJECTS
			
        var scene, camera, controls, pointLight, stats;
		var composer, renderer, mixer;
			
        var params = {
				exposure: 1.5,
				bloomStrength: 1.5,
				bloomThreshold: 0.06,
				bloomRadius: 0                
			};
			
        var clock = new THREE.Clock();
		var container = document.getElementById( 'container' );
			
        stats = new Stats();
		container.appendChild( stats.dom );
			
        renderer = new THREE.WebGLRenderer( { antialias: true } );
		renderer.setPixelRatio( window.devicePixelRatio );
		renderer.setSize( window.innerWidth, window.innerHeight );
		//renderer.toneMapping = THREE.ReinhardToneMapping;
		container.appendChild( renderer.domElement );
			
        scene = new THREE.Scene();
			
        camera = new THREE.PerspectiveCamera( 60, window.innerWidth / window.innerHeight, 1, 10000 );
		camera.position.set( 0, 700, - 1000 );
	    scene.add( camera );
			
        controls = new THREE.OrbitControls( camera, renderer.domElement );
		controls.maxPolarAngle = 360; //Math.PI * 0.5;
		controls.minDistance = 1;
		controls.maxDistance = 2000;
			
        scene.add( new THREE.AmbientLight( 0x404040, 1 ) );
			
        pointLight = new THREE.PointLight( 0xba15f4, 1000 );
	    //camera.add( pointLight );
			
        var renderScene = new THREE.RenderPass( scene, camera );
			
        var bloomPass = new THREE.UnrealBloomPass( new THREE.Vector2( window.innerWidth, window.innerHeight ), 1.5, 0.4, 0.85 );
		bloomPass.renderToScreen = true;
        bloomPass.exposure = params.exposure;
		bloomPass.threshold = params.bloomThreshold;
		bloomPass.strength = params.bloomStrength;
		bloomPass.radius = params.bloomRadius;
			
        composer = new THREE.EffectComposer( renderer );
		composer.setSize( window.innerWidth, window.innerHeight );
		composer.addPass( renderScene );
		composer.addPass( bloomPass );
            
        // Set up the sphere vars
        var SEGMENTS = 16;
        var RINGS = 16;
                
        spheres = new THREE.Object3D();

        var x = 0;

            for (var i = 0; i < jsonObj.length; i++) {
                    
                radius = jsonObj[x].Sun_Radius;
                var sphere = new THREE.SphereGeometry(radius, SEGMENTS, RINGS);
                suncolor = parseInt ( (jsonObj[x].Chromaticity_hex).replace("#","0x"), 16 );
                console.log(suncolor);
                
                
                var sunmat = new THREE.MeshStandardMaterial(
                    {
                    color: 0xcc0033,
                    emissive: 0xcc0033,
                    emissiveIntensity: 1000
                    });

                                  
                //creating the mesh and add primitive and material
                var sun = new THREE.Mesh(sphere, sunmat);
                //randomly set position and scale
                sun.position.x = jsonObj[x].Position_X;
                sun.position.y = jsonObj[x].Position_Z;   // Z & Y position need to be switched
                sun.position.z = jsonObj[x].Position_Y;
                //add particle to the spheres group
                spheres.add(sun);

                x++;
            }

        //correct spheres position relative to the camera
        spheres.position.y = 0;
        spheres.position.x = 0;
        spheres.position.z = 0;
        //add spheres to the scene
        scene.add(spheres);
        
        xplanes = new THREE.Object3D();

            for (var i = 0; i < 1000; i = i + 20 ) {
                
                var geometry = new THREE.CylinderGeometry(i,i,0.1,32);
                var xplanemat = new THREE.MeshStandardMaterial( {
                                                                color: 0x0F66A2, 
                                                                transparent: false, 
                                                                wireframe: true,
                                                                emissive: 0x0F66A2,
                                                                emissiveIntensity: (0.5-i*0.001)} );
                var cylinder = new THREE.Mesh( geometry, xplanemat );
                xplanes.add(cylinder);
            }
           
            
            scene.add(xplanes);                
    
        scene.add(xplanes);

        glxcenter = new THREE.Object3D();
    
            var geometry = new THREE.SphereGeometry(10,16,16);
            var glxmat = new THREE.MeshStandardMaterial( {
                                                                color: 0xffecaf, 
                                                                emissive: 0xffecaf,
                                                                emissiveIntensity: 10000} );
                                                                
            var glx = new THREE.Mesh( geometry, glxmat );
        glxcenter.add(glx);

        scene.add(glxcenter);



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

        var gui = new dat.GUI();
        gui.add( params, 'exposure', 0.1, 2 ).onChange( function ( value ) {
        renderer.toneMappingExposure = Math.pow( value, 4.0 );
        } );

        gui.add( params, 'bloomThreshold', 0.0, 1.0 ).onChange( function ( value ) {
        bloomPass.threshold = Number( value );
        } );

        gui.add( params, 'bloomStrength', 0.0, 3.0 ).onChange( function ( value ) {
        bloomPass.strength = Number( value );
        } );

        gui.add( params, 'bloomRadius', 0.0, 1.0 ).step( 0.01 ).onChange( function ( value ) {
        bloomPass.radius = Number( value );
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
			stats.update();
			composer.render();
			}

    }

	</script>





</body>
</html>
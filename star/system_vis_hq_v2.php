<!DOCTYPE html>
<html>
<head>
<title>System vis</title>

<link href="https://fonts.googleapis.com/css?family=Orbitron:400,700" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="lib/style/style.css">

</head>

<body style="background: black; overflow: hidden;">

<p id="loading" class="loading">loading SYSTEM VIS</p>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tween.js/16.3.5/Tween.min.js"></script>

<script src="lib/js/three.min.js"></script>
<script src="lib/js/OrbitControls.js"></script>
<!--script src="lib/js/libs/stats.min.js"></script-->
<!--script src="lib/js/libs/dat.gui.min.js"></script-->
<!--script src="lib/js/loaders/GLTFLoader.js"></script-->

<!--script src="lib/js/objects/Lensflare.js"></script-->

<script src="lib/js/postprocessing/EffectComposer.js"></script>
<script src="lib/js/postprocessing/RenderPass.js"></script>
<script src="lib/js/postprocessing/ShaderPass.js"></script>
<script src="lib/js/shaders/CopyShader.js"></script>
<script src="lib/js/shaders/LuminosityHighPassShader.js"></script>
<script src="lib/js/postprocessing/UnrealBloomPass.js"></script>
<script src='lib/js/threexFS.js'></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<div id="info" class="info">
 </div>

 <div id="controls" class="controls">
    <button id="flight" class="button" type="button">auto flight</button>
 </div>

<div id="container"></div>

<script type="x-shader/x-vertex" id="vertexshader">
			attribute float size;
			attribute vec3 customColor;
			varying vec3 vColor;
			void main() {
				vColor = customColor;
				vec4 mvPosition = modelViewMatrix * vec4( position, 1.0 );
				gl_PointSize = size * ( 300.0 / -mvPosition.z );
				gl_Position = projectionMatrix * mvPosition;
			}
</script>

<script type="x-shader/x-fragment" id="fragmentshader">
    		uniform vec3 color;
			uniform sampler2D texture;
			varying vec3 vColor;
			void main() {
				gl_FragColor = vec4( color * vColor, 1.0 );
				gl_FragColor = gl_FragColor * texture2D( texture, gl_PointCoord );
			}
</script>

<script>
       
    // GET DATA FROM PHP VIA AJAX    

var jsonObj;   

console.log('Fetching data from database');
var t0 = performance.now();
$.ajax({
    //type: "GET",
    url: 'system_vis_data_hq_v2.php',
    dataType: 'JSON',
    async: true,
        success: function(data){
        //console.log(data);
        jsonObj = data;
        simulation();
        }
}); 

function simulation() {   

    
    var t1 = performance.now();
    console.log('Data fetched in ' + Math.round(t1 - t0) + 'ms');
    console.log('Rendering simulation...');
    document.getElementById('loading').style.display = 'none';


	var scene, camera, controls, pointLight, stats, points;
	var xplanes, glxcenter, composer, renderer, uniforms;

    var params = {
			exposure: 0,
			bloomStrength: 5,
			bloomThreshold: 0,
			bloomRadius: 0.8               
	};			

    var clock = new THREE.Clock();
    var container = document.getElementById( 'container' );
    
    var scrollbar = 0;
    var windowheight = document.documentElement.clientHeight - scrollbar;
    var windowwidth = document.documentElement.clientWidth - scrollbar;
    console.log("Init window size: " + windowwidth + "x" + windowheight);
    
			
        //stats = new Stats();
		//container.appendChild( stats.dom );		

        //cubemap
		    	//var path = "lib/img/textures/MilkyWay/";
				//var format = '.jpg';
				//var urls = [
				//	path + 'px' + format, path + 'nx' + format,
				//	path + 'py' + format, path + 'ny' + format,
				//	path + 'pz' + format, path + 'nz' + format
				//];
				//var reflectionCube = new THREE.CubeTextureLoader().load( urls );
				//reflectionCube.format = THREE.RGBFormat;
	
    scene = new THREE.Scene();
    //scene.background = reflectionCube;
        //scene.background = new THREE.Color( 0xffffff, 0 );
			
    camera = new THREE.PerspectiveCamera( 60, windowwidth / windowheight, 1, 10000 );
	camera.position.set( 0, 500, - 1000 );
	scene.add( camera );

    renderer = new THREE.WebGLRenderer( { antialias: false } );
	renderer.setPixelRatio( window.devicePixelRatio );
	renderer.setSize( windowwidth, windowheight );
    //renderer.setClearColor( 0x000000, 0 ); 
    container.appendChild( renderer.domElement );
			
    controls = new THREE.OrbitControls( camera, renderer.domElement );
	controls.maxPolarAngle = 360; 
	controls.minDistance = 1;
	controls.maxDistance = 10000;
			
        //scene.add( new THREE.AmbientLight( 0x404040, 10000 ) );			
        //pointLight = new THREE.PointLight( 0xba15f4, 10 );	

	var renderScene = new THREE.RenderPass( scene, camera );
			
        var bloomPass = new THREE.UnrealBloomPass( new THREE.Vector2( windowwidth, windowheight ), 0, 0, 0 );
        bloomPass.renderToScreen = true;
        bloomPass.exposure = params.exposure;
        bloomPass.threshold = params.bloomThreshold;
        bloomPass.strength = params.bloomStrength;
        bloomPass.radius = params.bloomRadius;

        composer = new THREE.EffectComposer( renderer );
		composer.setSize( windowwidth, windowheight );
		composer.addPass( renderScene );
		composer.addPass( bloomPass );  

        var geometry = new THREE.BufferGeometry();
        var color = new THREE.Color();
        var positions = [];
        var colors = new Float32Array( jsonObj.length * 3 );
        var radius = new Float32Array( jsonObj.length );
            
        uniforms = {
				color:     { value: new THREE.Color( 0xffffff ) },
				texture:   { value: new THREE.TextureLoader().load( "lib/img/sprites/spark0.png" ) }
			};

        var shaderMaterial = new THREE.ShaderMaterial( {
				uniforms:       uniforms,
				vertexShader:   document.getElementById( 'vertexshader' ).textContent,
				fragmentShader: document.getElementById( 'fragmentshader' ).textContent,
				blending:       THREE.AdditiveBlending,
				depthTest:      false,
				transparent:    true
			});
     
            for (var i = 0, i3 = 0; i < jsonObj.length; i++, i3 += 3) {

                    // positions 
                    var x = jsonObj[i].Position_X;            
                    var y = jsonObj[i].Position_Z;   // Z & Y position need to be switched
                    var z = jsonObj[i].Position_Y;
                    
                    
                    positions.push( x, y, z );
                    
                    radius[i] = jsonObj[i].Sun_Radius * 15;

                    var r = jsonObj[i].HSL_H;
                    var g = jsonObj[i].HSL_S;
                    var b = jsonObj[i].HSL_L;   
                  
                    color.setHSL( r, g, b );
				    colors[ i3 + 0 ] = color.r;
				    colors[ i3 + 1 ] = color.g;
				    colors[ i3 + 2 ] = color.b;
                    
            }
            
            geometry.addAttribute( 'position', new THREE.Float32BufferAttribute( positions, 3 ) );
            geometry.addAttribute( 'customColor', new THREE.BufferAttribute( colors, 3 ) );
            geometry.addAttribute( 'size', new THREE.BufferAttribute( radius, 1 ) );            
            geometry.computeBoundingSphere();

            points = new THREE.Points( geometry, shaderMaterial );
            
            scene.add( points );

            console.log("Init-positions created!");  

    //xplanes = new THREE.Object3D();

      //      for (var i = 0; i < 1000; i = i + 20 ) {
        
       //         var geometry = new THREE.CylinderGeometry(i,i,0.1,32);
       //         var xplanemat = new THREE.MeshStandardMaterial( {
       //                                                 color: 0x0F66A2, 
       //                                                 transparent: false, 
       //                                                 wireframe: true,
       //                                                 emissive: 0x0F66A2,
       //                                                 emissiveIntensity: (0.5-i*0.001)} );
       //         var cylinder = new THREE.Mesh( geometry, xplanemat );
       //         xplanes.add(cylinder);
       // }


    //scene.add(xplanes);            

        
    glxcenter = new THREE.Object3D();
    
        var center = new THREE.SphereGeometry(100,16,16);
        var glxmat = new THREE.MeshStandardMaterial( {
                                                        color: 0xffecaf, 
                                                        emissive: 0xffecaf,
                                                        emissiveIntensity: 100} );
                                                                
        var glx = new THREE.Mesh( center, glxmat );
        glxcenter.add(glx);

    scene.add(glxcenter);
    
    window.onresize = function () {  
        windowwidth = window.innerWidth-scrollbar;
		windowheight = window.innerHeight-scrollbar;
		camera.aspect = windowwidth / windowheight;
		camera.updateProjectionMatrix();
		renderer.setSize( windowwidth, windowheight );
		composer.setSize( windowwidth, windowheight );
		};

    var zero = new THREE.Vector3();    
    var vector, distance, velocity, px, py, origin;

            //origin = geometry.getAttribute('position').array;
            //console.log('x=' + geometry.attributes.position.getX( 0 ) + 'y=' + geometry.attributes.position.getY( 0 ) + 'z=' + geometry.attributes.position.getZ( 0 ));

    console.log("Starting star movement...");

    

var updatePosition = function () {

            for (var i = 0; i < jsonObj.length; i++) {

                    vector = new THREE.Vector3(geometry.attributes.position.getX( i ), geometry.attributes.position.getY( i ), geometry.attributes.position.getZ( i ))
                    //console.log('x=' + geometry.attributes.position.getX( i ) + 'y=' + geometry.attributes.position.getY( i ) + 'z=' + geometry.attributes.position.getZ( i ));
                    //console.log(vector);
                    //console.log(zero);

                    distance = vector.distanceTo( zero );
                    //console.log(distance);
                    velocity = (10000/distance)/1000;
                    px = geometry.attributes.position.getX( i ) * Math.cos(velocity * (Math.PI / 180)) - geometry.attributes.position.getZ( i ) * Math.sin(velocity * (Math.PI / 180));
                    //console.log(velocity * (Math.PI / 180));
                    
                    //console.log((distance * (Math.PI / 180)));
                    //console.log(px);
                    py = geometry.attributes.position.getX( i ) * Math.sin(velocity * (Math.PI / 180)) + geometry.attributes.position.getZ( i ) * Math.cos(velocity * (Math.PI / 180));
                    //console.log(py);       
                    geometry.attributes.position.setXYZ( i, px, geometry.attributes.position.getY( i ), py ); // alternate

                    //console.log('x=' + geometry.attributes.position.getX( i ) + 'y=' + geometry.attributes.position.getY( i ) + 'z=' + geometry.attributes.position.getZ( i ));
                    // only required if geometry previously-rendered
                    }

            geometry.attributes.position.needsUpdate = true;
    }
  
function getRndInteger(min, max) {
  return Math.floor(Math.random() * (max - min) ) + min;
}

var m = 0;
         
var lookAt = function(m) {


    i = Math.floor(Math.random() * jsonObj.length);
    var cx = geometry.attributes.position.getX(i);// - 50;
    var cy = geometry.attributes.position.getY(i);// - 50;
    var cz = geometry.attributes.position.getZ(i);// - 250;
    var lx = cx;
    var ly = cy;
    var lz = cz;
    console.log(cx + " " + cy);
    console.log(Math.pow(cx,2));
    
    
    if (Math.pow(cx,2) < 10000) {

        console.log("kleiner");
        movement(cx, cy, cz, lx, ly, lz);        
        
    } else {
        
        console.log("größer");        
        cx = getRndInteger(-10, 10);
        cy = 500;//getRndInteger(-500, 500);
        cz = -500;
        lx = 0;
        ly = 0;
        lz = 0;
        movement(cx, cy, cz, lx, ly, lz);
    }
  
    //SystemData(i);	

}

var movement = function(cx, cy, cz, lx, ly, lz) {
        // backup original rotation
        var startRotation = camera.quaternion.clone();

        // final rotation (with lookAt)
        camera.lookAt( lx, ly, lz );
        
        var endRotation = camera.quaternion.clone();

        // revert to original rotation
        camera.quaternion.copy( startRotation );

        // Tween
        var clook = new TWEEN.Tween( camera.quaternion )
            .to( endRotation, 2000 )
            .easing(TWEEN.Easing.Sinusoidal.Out);

        //console.log("lookAt: " + geometry.attributes.position.getX(i) + " " + geometry.attributes.position.getY(i) + " " + geometry.attributes.position.getZ(i));
        console.log("Looking at System: " + jsonObj[i].System_Name);
        
        var cmove = new TWEEN.Tween(camera.position)
            .to({ x: cx, y: cy, z: cz }, 2000)
            .easing(TWEEN.Easing.Sinusoidal.InOut)
            .start();
            
        clook.chain(cmove);
        
        clook.start();
}

function SystemData (i) {
    info = "<p class='infotext'>System Name:   " + jsonObj[i].System_Name
            + "<br>" + 
            "Sun Name:   " + jsonObj[i].Sun_Name
            + "<br>" + 
            "Sun Class:   " + jsonObj[i].SUN_CLASS
            + "<br>" + 
            "Sun Temperature:   " + jsonObj[i].Sun_Temp
            + "<br>" + 
            "Sun Mass:   " + jsonObj[i].Sun_Mass
            + "<br>" + 
            "Sun Radius:   " + jsonObj[i].Sun_Radius
            + "</p>";
    document.getElementById('info').innerHTML=info;
}

var animate = function () {
			
    requestAnimationFrame( animate );
            
    updatePosition();
        //var speed = Date.now() * 0.00001;         
        //points.rotation.y = speed;      
        //stats.update();
	
    TWEEN.update();
    
    m++;

    if (m <= 1000) {
        //console.log(m);
    } else {
        m = 0;
        lookAt(m);
        
    }      

    composer.render( scene, camera );
            
}

    animate();
    
    
        


    }
			
</script>

</body>
</html>
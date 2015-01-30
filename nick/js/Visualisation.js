function init() {
	var data = {
		"nodes" : [{
			"x" : 469,
			"y" : 410,
			"z" : 600,
			"label" : 1
		}, {
			"x" : 493,
			"y" : 364,
			"z" : 14,
			"label":"2"
		}, {
			"x" : 442,
			"y" : 365,
			"z" : 123,
			"label":"3"
		}, {
			"x" : 467,
			"y" : 314,
			"z" : 80,
			"label":"4"
		}, {
			"x" : 477,
			"y" : 248,
			"z" : 56,
			"label":"5"
		}, {
			"x" : 425,
			"y" : 207,
			"z" : 156,
			"label":"6"
		}, {
			"x" : 402,
			"y" : 155,
			"z" : 231,
			"label":"7"
		}, {
			"x" : 369,
			"y" : 196,
			"z" : 201,
			"label":"8"
		}, {
			"x" : 350,
			"y" : 148,
			"z" : 180,
			"label":"9"
		}, {
			"x" : 539,
			"y" : 222,
			"z" : 234,
			"label":"10"
		}, {
			"x" : 594,
			"y" : 235,
			"z" : 265,
			"label":"11"
		}, {
			"x" : 582,
			"y" : 185,
			"z" : 10,
			"label":"12"
		}, {
			"x" : 633,
			"y" : 200,
			"z" : 100,
			"label":"13"
		}, {
			"x" : 0,
			"y" : 0,
			"z" : 104,
			"label":"14"
		}],
		"links" : [{
			"source" : 0,
			"target" : 1
		}, {
			"source" : 1,
			"target" : 2
		}, {
			"source" : 2,
			"target" : 0
		}, {
			"source" : 1,
			"target" : 3
		}, {
			"source" : 3,
			"target" : 2
		}, {
			"source" : 3,
			"target" : 4
		}, {
			"source" : 4,
			"target" : 5
		}, {
			"source" : 5,
			"target" : 6
		}, {
			"source" : 5,
			"target" : 7
		}, {
			"source" : 6,
			"target" : 7
		}, {
			"source" : 6,
			"target" : 8
		}, {
			"source" : 7,
			"target" : 8
		}, {
			"source" : 9,
			"target" : 4
		}, {
			"source" : 9,
			"target" : 11
		}, {
			"source" : 9,
			"target" : 10
		}, {
			"source" : 10,
			"target" : 11
		}, {
			"source" : 11,
			"target" : 12
		}, {
			"source" : 12,
			"target" : 10
		}, {
			"source" : 7,
			"target" : 13
		}]
	};
	// Set visualisation variables
	var WIDTH = 1000,
	    HEIGHT = 600;
	COLOR = "steelblue";
	LINK_COLOR = "#cccccc";

	// Create scene
	var scene = new THREE.Scene();

	// set some camera attributes
	var VIEW_ANGLE = 45,
	    ASPECT = WIDTH / HEIGHT,
	    NEAR = 0.1,
	    FAR = 10000;

	// Set variable for container
	var $container = $('#canvas-svg');

	// Create Renderer
	var renderer = new THREE.WebGLRenderer({
		alpha : true,
		antialiasing : true
	});
	renderer.setClearColor(0x000000, 0);
	renderer.setSize(WIDTH, HEIGHT);

	// Create camera
	var camera = new THREE.PerspectiveCamera(VIEW_ANGLE, ASPECT, NEAR, FAR);
	scene.add(camera);
	camera.position.z = 300;

	// Create controls (orbitcontrols)
	var controls = new THREE.OrbitControls(camera);

	// Attach the render-supplied DOM element
	$container.append(renderer.domElement);

	// Create arrays for spheres and links
	var spheres = [],
	    three_links = [];
	    labels = [];

	// Define the 3d force
	var force = d3.layout.force3d().nodes( sort_data = []).links( links = []).size([50, 50]).gravity(0.3).charge(-400);
	var DISTANCE = 1;

	//initControls();
	createNodes();
	initForce3D();
	animate();
	
	function createNodes() {
		for (var i = 0; i < data.nodes.length; i++) {
			sort_data.push({
				x : data.nodes.x + DISTANCE,
				y : data.nodes.y + DISTANCE,
				z : data.nodes.z + DISTANCE
			});

			// set up the sphere vars
			var radius = 5,
			    segments = 16,
			    rings = 16;

			// create the sphere's material
			var sphereMaterial = new THREE.MeshLambertMaterial({
				color : COLOR
			});

			var sphere = new THREE.Mesh(new THREE.SphereGeometry(radius, segments, rings), sphereMaterial);

			spheres.push(sphere);

			// add the sphere to the scene
			scene.add(sphere);
			
			var canvas1 = document.createElement('canvas');
				//canvas1.width = 500;
				//canvas1.height = 500;
			var context1 = canvas1.getContext('2d');
				context1.font = "Bold 10px Arial";
				context1.fillStyle = "rgba(255,0,0,0.95)";
				context1.fillText('Hello, world! ' + data.nodes[i].label, 0, 50);
			var texture1 = new THREE.Texture(canvas1);
				texture1.needsUpdate = true;
				texture1.magFilter = THREE.NearestFilter;
				texture1.minFilter = THREE.LinearMipMapLinearFilter;
			var material1 = new THREE.MeshBasicMaterial({
				map : texture1,
				side : THREE.DoubleSide
			});
				material1.transparent = true;
			var mesh1 = new THREE.Mesh(new THREE.PlaneGeometry(300, 100), material1);
			
			labels.push(mesh1);
			
			scene.add(mesh1);
			
					// /////// draw text on canvas /////////
		// // create a canvas element
		// 
		// //console.log(data.nodes[i].label);
		// // canvas contents will be used for a texture
		// var texture1 = new THREE.Texture(canvas1);
		// texture1.needsUpdate = true;
		// texture1.magFilter = THREE.NearestFilter;
		// texture1.minFilter = THREE.LinearMipMapLinearFilter;
		// var material1 = new THREE.MeshBasicMaterial({
			// map : texture1,
			// side : THREE.DoubleSide
		// });
		// material1.transparent = true;
		// var mesh1 = new THREE.Mesh(new THREE.PlaneGeometry(300, 100), material1);
		// //mesh1.position.set(Math.floor((Math.random() * 100) + 1), Math.floor((Math.random() * 100) + 1), Math.floor((Math.random() * 100) + 1));
		// mesh1.position = new THREE.Vector3(data.nodes[i].x + DISTANCE, data.nodes[i].y + DISTANCE, data.nodes[i].z);
		// scene.add(mesh1);
		}


		for (var i = 0; i < data.links.length; i++) {
			links.push({
				target : sort_data[data.links[i].target],
				source : sort_data[data.links[i].source]
			});

			var material = new THREE.LineBasicMaterial({
				color : LINK_COLOR,
				linewidth : 2
			});
			var geometry = new THREE.Geometry();

			geometry.vertices.push(new THREE.Vector3(0, 0, 0));
			geometry.vertices.push(new THREE.Vector3(0, 0, 0));
			var line = new THREE.Line(geometry, material);
			line.userData = {
				source : data.links[i].source,
				target : data.links[i].target
			};
			three_links.push(line);
			scene.add(line);

			force.start();
		}
	}

	function initForce3D() {
		// set up the axes
		var x = d3.scale.linear().domain([0, 350]).range([0, 10]),
		    y = d3.scale.linear().domain([0, 350]).range([0, 10]),
		    z = d3.scale.linear().domain([0, 350]).range([0, 10]);

		force.on("tick", function(e) {
			for (var i = 0; i < sort_data.length; i++) {
				spheres[i].position.set(x(sort_data[i].x) * 40 - 40, y(sort_data[i].y) * 40 - 40, z(sort_data[i].z) * 40 - 40);
				labels[i].position.set(x(sort_data[i].x) * 40 - 40, y(sort_data[i].y) * 40 - 40, z(sort_data[i].z) * 40 - 40);

				for (var j = 0; j < three_links.length; j++) {
					var line = three_links[j];
					var vi = -1;
					if (line.userData.source === i) {
						vi = 0;
					}
					if (line.userData.target === i) {
						vi = 1;
					}

					if (vi >= 0) {
						line.geometry.vertices[vi].x = x(sort_data[i].x) * 40 - 40;
						line.geometry.vertices[vi].y = y(sort_data[i].y) * 40 - 40;
						line.geometry.vertices[vi].z = y(sort_data[i].z) * 40 - 40;
						line.geometry.verticesNeedUpdate = true;
					}
				}
			}

			renderer.render(scene, camera);
		});
	}

	function animate() {
		requestAnimationFrame(animate);
		renderer.render(scene, camera);
		controls.update();
	}

	function render() {
		renderer.render(scene, camera);
		animate();
	}

}
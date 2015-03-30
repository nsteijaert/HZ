function init() {
	var data = {
		"nodes" : [{
			"x" : 469,
			"y" : 410,
			"z" : 600,
			"label" : "dijk"
		}, {
			"x" : 493,
			"y" : 364,
			"z" : 14,
			"label" : "brug"
		}, {
			"x" : 442,
			"y" : 365,
			"z" : 123,
			"label" : "zee"
		}, {
			"x" : 467,
			"y" : 314,
			"z" : 80,
			"label" : "oester"
		}, {
			"x" : 477,
			"y" : 248,
			"z" : 56,
			"label" : "dam"
		}, {
			"x" : 425,
			"y" : 207,
			"z" : 156,
			"label" : "bekken"
		}, {
			"x" : 402,
			"y" : 155,
			"z" : 231,
			"label" : "reservoir"
		}, {
			"x" : 369,
			"y" : 196,
			"z" : 201,
			"label" : "duin"
		}, {
			"x" : 350,
			"y" : 148,
			"z" : 180,
			"label" : "golfbreker"
		}, {
			"x" : 539,
			"y" : 222,
			"z" : 234,
			"label" : "vis"
		}, {
			"x" : 594,
			"y" : 235,
			"z" : 265,
			"label" : "zeewier"
		}, {
			"x" : 582,
			"y" : 185,
			"z" : 10,
			"label" : "koraal"
		}, {
			"x" : 633,
			"y" : 200,
			"z" : 100,
			"label" : "sluis"
		}, {
			"x" : 0,
			"y" : 0,
			"z" : 104,
			"label" : "slot"
		}],
		"links" : [{
			"source" : 0,
			"target" : 1,
			"type" : "sibling"
		}, {
			"source" : 1,
			"target" : 2,
			"type" : "parent"
		}, {
			"source" : 2,
			"target" : 0,
			"type" : "analogy"
		}, {
			"source" : 1,
			"target" : 3,
			"type" : "child"
		}, {
			"source" : 3,
			"target" : 2,
			"type" : "sibling"
		}, {
			"source" : 3,
			"target" : 4,
			"type" : "child"
		}, {
			"source" : 4,
			"target" : 5,
			"type" : "sibling"
		}, {
			"source" : 5,
			"target" : 6,
			"type" : "parent"
		}, {
			"source" : 5,
			"target" : 7,
			"type" : "child"
		}, {
			"source" : 2,
			"target" : 5,
			"type" : "child"
		}, {
			"source" : 6,
			"target" : 7,
			"type" : "sibling"
		}, {
			"source" : 6,
			"target" : 8,
			"type" : "sibling"
		}, {
			"source" : 7,
			"target" : 8,
			"type" : "child"
		}, {
			"source" : 9,
			"target" : 4,
			"type" : "sibling"
		}, {
			"source" : 4,
			"target" : 9,
			"type" : "sibling"
		}, {
			"source" : 9,
			"target" : 11,
			"type" : "parent"
		}, {
			"source" : 9,
			"target" : 10,
			"type" : "child"
		}, {
			"source" : 10,
			"target" : 11,
			"type" : "sibling"
		}, {
			"source" : 11,
			"target" : 12,
			"type" : "sibling"
		}, {
			"source" : 12,
			"target" : 10,
			"type" : "parent"
		}, {
			"source" : 7,
			"target" : 13,
			"type" : "child"
		}]
	};

	// Set visualisation variables
	var WIDTH = $(window).width();
	HEIGHT = $(window).height();
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

	// create a point light
	var pointLight1 = new THREE.PointLight(0xFFFFFF);
	var pointLight2 = new THREE.PointLight(0xFFFFFF);
	var pointLight3 = new THREE.PointLight(0xFFFFFF);
	var pointLight4 = new THREE.PointLight(0xFFFFFF);
	var pointLight5 = new THREE.PointLight(0xFFFFFF);

	// set its position
	pointLight1.position.x = 0;
	pointLight1.position.y = 50;
	pointLight1.position.z = 500;

	pointLight2.position.x = 0;
	pointLight2.position.y = 500;
	pointLight2.position.z = -500;

	pointLight3.position.x = 500;
	pointLight3.position.y = 500;
	pointLight3.position.z = 0;

	pointLight4.position.x = -500;
	pointLight4.position.y = 50;
	pointLight4.position.z = 0;

	pointLight4.position.x = 0;
	pointLight4.position.y = -100;
	pointLight4.position.z = 0;

	// add to the scene
	scene.add(pointLight1);
	scene.add(pointLight2);
	scene.add(pointLight3);
	scene.add(pointLight4);

	//initControls();
	createNodes();
	initForce3D();
	animate();

	// Picking stuff
	projector = new THREE.Projector();
	mouseVector = new THREE.Vector3();
	// User interaction
	window.addEventListener('mousemove', onMouseMove, false);
	window.addEventListener('resize', onWindowResize, false);

	function createNodes() {
		for (var i = 0; i < data.nodes.length; i++) {
			sort_data.push({
				x : data.nodes.x + DISTANCE,
				y : data.nodes.y + DISTANCE,
				z : data.nodes.z + DISTANCE
			});

			// set up the sphere vars
			var radius = 5,
			    segments = 32,
			    rings = 32;

			// create the sphere's material
			var sphereMaterial = new THREE.MeshLambertMaterial({
				color : COLOR
			});

			var sphere = new THREE.Mesh(new THREE.SphereGeometry(radius, segments, rings), sphereMaterial);
			sphere.name = data.nodes[i].label;
			console.log(sphere);
			spheres.push(sphere);

			// add the sphere to the scene
			scene.add(sphere);

			var canvas1 = document.createElement('canvas');
			var context1 = canvas1.getContext('2d');
			context1.font = "Bold 30px Arial";
			context1.fillStyle = "rba(0,0,0,0.95)";
			context1.fillText(data.nodes[i].label, 0, 20);
			var texture1 = new THREE.Texture(canvas1);
			texture1.needsUpdate = true;
			texture1.magFilter = THREE.NearestFilter;
			texture1.minFilter = THREE.LinearMipMapLinearFilter;
			var material1 = new THREE.MeshBasicMaterial({
				map : texture1,
				side : THREE.DoubleSide
			});
			material1.transparent = true;
			var mesh1 = new THREE.Mesh(new THREE.PlaneGeometry(40, 15), material1);

			labels.push(mesh1);

			scene.add(mesh1);
		}
		for (var i = 0; i < data.links.length; i++) {
			links.push({
				target : sort_data[data.links[i].target],
				source : sort_data[data.links[i].source],
				type : sort_data[data.links[i].type]
			});
			var origin = new THREE.Vector3(0, 0, 0);
			var terminus = new THREE.Vector3(20, 20, 20);
			var direction = new THREE.Vector3().subVectors(terminus, origin).normalize();
			var distance = origin.distanceTo(terminus);
			var color = new THREE.Color("rgb(0,0,0)");
			var headLength = 10;
			var headWidth = 5;
			var arrow = new THREE.ArrowHelper(direction, origin, distance, color, headLength, headWidth);
			//arrows.push(arrow);
			//console.log(distance);
			console.log(arrow);
			scene.add(arrow);
			arrow.userData = {
				source : data.links[i].source,
				target : data.links[i].target
			};
			three_links.push(arrow);
			scene.add(arrow);
		}
		force.start();
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
					var arrow = three_links[j];
					if (arrow.userData.source === i) {
						var x_arrow = x(sort_data[i].x) * 40 - 40;
						var y_arrow = y(sort_data[i].y) * 40 - 40;
						var z_arrow = z(sort_data[i].z) * 40 - 40;
						var new_origin = new THREE.Vector3(x_arrow, y_arrow, z_arrow);
						arrow.position = new_origin;
					}
					if (arrow.userData.target === i) {
						var x_arrow_cur = arrow.position.x;
						var y_arrow_cur = arrow.position.y;
						var z_arrow_cur = arrow.position.z;
						var cur_pos= new THREE.Vector3(x_arrow_cur, y_arrow_cur, z_arrow_cur);
						var x_arrow_tar = x(sort_data[i].x) * 40 - 40;
						var y_arrow_tar = y(sort_data[i].y) * 40 - 40;
						var z_arrow_tar = z(sort_data[i].z) * 40 - 40;
						var newTarget = new THREE.Vector3(x_arrow_tar, y_arrow_tar, z_arrow_tar);
						var direction = new THREE.Vector3().sub(newTarget, cur_pos);
						arrow.setDirection(direction.normalize());
						arrow.setLength(cur_pos.distanceTo(newTarget)-5, 10, 5);
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

		for (var i = 0; i < labels.length; i++) {
			labels[i].lookAt(camera.position);
		}
		render();
	}

	function render() {

	}

	function onMouseMove(e) {

		mouseVector.x = 2 * (e.clientX / WIDTH) - 1;
		mouseVector.y = 1 - 2 * (e.clientY / HEIGHT);
		var raycaster = projector.pickingRay(mouseVector.clone(), camera),
		    intersects;
		var intersects = raycaster.intersectObjects(spheres);
		for (var i = 0; i < intersects.length; i++) {
			var intersection = intersects[i],
			    obj = intersection.object;
			obj.material.color.set("red");
			console.log(obj.name);
		}
	}

	function onWindowResize(e) {
		WIDTH = $(window).width();
		HEIGHT = $(window).height();
		renderer.setSize(WIDTH, HEIGHT);
		camera.aspect = WIDTH / HEIGHT;
		camera.updateProjectionMatrix();
	}

	function convertVectorsToDist(source, target) {

		var dx = source.x - target.x;
		var dy = source.y - target.y;
		var dz = source.z - target.z;
		return sqrt(dx * dx + dy * dy + dz * dz);
	}

	function convertVectorsToDir(source, target) {

		var dx = source.x - target.x;
		var dy = source.y - target.y;
		var dz = source.z - target.z;
		return sqrt(dx * dx + dy * dy + dz * dz);
	}

	function constructArrowHelper(source, target) {
		var origin = new THREE.Vector3(50, 100, 50);
		var terminus = new THREE.Vector3(75, 75, 75);
		var direction = new THREE.Vector3().subVectors(terminus, origin).normalize();
		var distance = origin.distanceTo(terminus);
		var arrow = new THREE.ArrowHelper(direction, origin, distance, 0x884400);
		//arrows.push(arrow);
		console.log(distance);
		console.log(arrow);
		scene.add(arrow);
	}

}
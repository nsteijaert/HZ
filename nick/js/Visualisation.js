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

	//mouse event variables
	var projector = new THREE.Projector(),
	    mouse_vector = new THREE.Vector3(),
	    mouse = {
		x : 0,
		y : 0,
		z : 1
	},
	    ray = new THREE.Raycaster(new THREE.Vector3(0, 0, 0), new THREE.Vector3(0, 0, 0)),
	    intersects = [];

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
	loadOptions();
	createNodes();
	initForce3D();
	animate();

	//we add the even listener function to the domElement
	renderer.domElement.addEventListener('mousedown', onMouseDown);

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
				source : sort_data[data.links[i].source]
			});
			var type = data.links[i].type;
			switch(type) {
			case "sibling":
				var material = new THREE.LineBasicMaterial({
					color : "green",
					linewidth : 3
				});
				break;
			case "parent":
				var material = new THREE.LineBasicMaterial({
					color : "red",
					linewidth : 3
				});
				break;
			case "child":
				var material = new THREE.LineBasicMaterial({
					color : "blue",
					linewidth : 3
				});
				break;
			case "analogy":
				var material = new THREE.LineBasicMaterial({
					color : "yellow",
					linewidth : 3
				});
				break;
			default:
				var material = new THREE.LineBasicMaterial({
					color : "black",
					linewidth : 3
				});
			}
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

	function loadOptions(depth, relations) {
		var currentDepth = depth;
		var currentRelations = relations.split(",");

		$("#options .depth").val(currentDepth);
		$("#options .broader").prop("checked", (currentRelations[0] === "true"));
		$("#options .narrower").prop("checked", (currentRelations[1] === "true"));
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

		for (var i = 0; i < labels.length; i++) {
			labels[i].lookAt(camera.position);
		}
		render();
	}

	function render() {

	}

	//event listener
	function onMouseDown(event_info) {
		console.log(spheres.children);
		//stop any other event listener from recieving this event
		event_info.preventDefault();

		//this where begin to transform the mouse cordinates to three,js cordinates
		mouse.x = (event_info.clientX / window.innerWidth ) * 2 - 1;
		mouse.y = -(event_info.clientY / window.innerHeight ) * 2 + 1;

		//this vector caries the mouse click cordinates
		mouse_vector.set(mouse.x, mouse.y, mouse.z);

		//the final step of the transformation process, basically this method call
		//creates a point in 3d space where the mouse click occurd
		projector.unprojectVector(mouse_vector, camera);

		var direction = mouse_vector.sub(camera.position).normalize();

		//ray = new THREE.Raycaster( camera.position, direction );
		ray.set(camera.position, direction);

		//asking the raycaster if the mouse click touched the sphere object
		intersects = ray.intersectObject();

		//the ray will return an array with length of 1 or greater if the mouse click
		//does touch the sphere object
		if (intersects.length) {

			alert("hit");

		}

	}

}
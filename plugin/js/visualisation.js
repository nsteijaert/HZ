/**
 * @author NJK
 */
//Wait for document to finish loading
$(document).ready(function() {

	function load() {
		$('.visualisationResult').append('<div class="bar"></div>');
		$bar = $('.bar');
		$bar.animate({
			width : '100%'
		}, 1000, "swing", function() {

		});
	}

	//console.log($.cookie("depth"));
	if ($.cookie("depth") != "" && $.cookie("relations") != "")
		visualize("TZW:gezicht", $.cookie("depth"), $.cookie("relations"));
	else
		visualize("TZW:gezicht");

	function visualize(concept, depth, relations) {
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

		// Raypicking
		projector = new THREE.Projector();
		mouseVector = new THREE.Vector3();

		// User interaction
		//window.addEventListener('mousemove', onMouseMove, false);
		//window.addEventListener('resize', onWindowResize, false);

		if ( typeof concept === 'undefined' || concept === '') {
			throw "Concept is undefined";
		}
		var depth = typeof depth !== 'undefined' ? depth : 1;
		var relations = typeof relations !== 'undefined' ? relations : "true,true";
		var timeOut;
		var mouseTimeOut;

		// Clear div
		d3.select(".visualisation").html("");

		var width = $('.visualisation').width(),
		    height = 500;

		$.ajax({
			type : "POST",
			cache : false,
			url : "php/VisualisationScript.php",
			async : true,
			data : {
				concept : concept,
				depth : depth.toString(),
				relations : relations
			}
		}).done(function(data) {
			loadOptions(depth, relations);

			var links = JSON.parse(data);
			var nodes = {};

			// Compute the distinct nodes from the links.
			links.forEach(function(link) {
				link.source = nodes[link.source] || (nodes[link.source] = {
					name : link.source,
					url : link.urlsource
				});
				link.target = nodes[link.target] || (nodes[link.target] = {
					name : link.target,
					url : link.urltarget
				});
			});

			console.log(Object.keys(nodes).length);
			
			//initControls();
			createNodes();
			initForce3D();
			animate();

			// Functions
			function createNodes() {
				console.log("createNodes called");
				
				for (var key in nodes) {
					if (nodes.hasOwnProperty(key)) {
						var val = nodes[key];
						nodes[key].x = 0;
						nodes[key].y = 0;
						nodes[key].z = 0;
						console.log(val);

					// set up the sphere vars
					var radius = 5,
						segments = 32,
						rings = 32;

					// create the sphere's material
					var sphereMaterial = new THREE.MeshLambertMaterial({
						color : COLOR
					});

					var sphere = new THREE.Mesh(new THREE.SphereGeometry(radius, segments, rings), sphereMaterial);
					sphere.name = nodes[key].name;
					console.log(sphere);
					spheres.push(sphere);

					// add the sphere to the scene
					scene.add(sphere);

					var canvas1 = document.createElement('canvas');
					var context1 = canvas1.getContext('2d');
					context1.font = "Bold 30px Arial";
					context1.fillStyle = "rba(0,0,0,0.95)";
					context1.fillText(nodes[key].name, 0, 20);
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
				}
				// for (var i = 0; i < data.links.length; i++) {
					// links.push({
						// target : sort_data[data.links[i].target],
						// source : sort_data[data.links[i].source],
						// type : sort_data[data.links[i].type]
					// });
					// var origin = new THREE.Vector3(0, 0, 0);
					// var terminus = new THREE.Vector3(20, 20, 20);
					// var direction = new THREE.Vector3().subVectors(terminus, origin).normalize();
					// var distance = origin.distanceTo(terminus);
					// var color = new THREE.Color("rgb(0,0,0)");
					// var headLength = 10;
					// var headWidth = 5;
					// var arrow = new THREE.ArrowHelper(direction, origin, distance, color, headLength, headWidth);
					// //arrows.push(arrow);
					// //console.log(distance);
					// console.log(arrow);
					// scene.add(arrow);
					// arrow.userData = {
						// source : data.links[i].source,
						// target : data.links[i].target
					// };
					// three_links.push(arrow);
					// scene.add(arrow);
				// }
				force.start();
			}

			function loadOptions(depth, relations) {
				var currentDepth = depth;
				var currentRelations = relations.split(",");

				$("#options .depth").val(currentDepth);
				$("#options .broader").prop("checked", (currentRelations[0] === "true"));
				$("#options .narrower").prop("checked", (currentRelations[1] === "true"));
			}

			function initForce3D() {
				console.log('force 3D initializing');
				// set up the axes
				var x = d3.scale.linear().domain([0, 350]).range([0, 10]),
				    y = d3.scale.linear().domain([0, 350]).range([0, 10]),
				    z = d3.scale.linear().domain([0, 350]).range([0, 10]);

				force.on("tick", function(e) {
					for (var i = 0; i < sort_data.length; i++) {
						console.log(spheres);
						spheres[i].position.set(x(nodes[key].x) * 40 - 40, y(nodes[key].y) * 40 - 40, z(nodes[key].z) * 40 - 40);
						labels[i].position.set(x(nodes[key].x) * 40 - 40, y(nodes[key].y) * 40 - 40, z(nodes[key].z) * 40 - 40);

						// for (var j = 0; j < three_links.length; j++) {
							// var arrow = three_links[j];
							// if (arrow.userData.source === i) {
								// var x_arrow = x(nodes[key].x) * 40 - 40;
								// var y_arrow = y(nodes[key].y) * 40 - 40;
								// var z_arrow = z(nodes[key].z) * 40 - 40;
								// var new_origin = new THREE.Vector3(x_arrow, y_arrow, z_arrow);
								// arrow.position = new_origin;
							// }
							// if (arrow.userData.target === i) {
								// var x_arrow_cur = arrow.position.x;
								// var y_arrow_cur = arrow.position.y;
								// var z_arrow_cur = arrow.position.z;
								// var cur_pos = new THREE.Vector3(x_arrow_cur, y_arrow_cur, z_arrow_cur);
								// var x_arrow_tar = x(sort_data[i].x) * 40 - 40;
								// var y_arrow_tar = y(sort_data[i].y) * 40 - 40;
								// var z_arrow_tar = z(sort_data[i].z) * 40 - 40;
								// var newTarget = new THREE.Vector3(x_arrow_tar, y_arrow_tar, z_arrow_tar);
								// var direction = new THREE.Vector3().sub(newTarget, cur_pos);
								// arrow.setDirection(direction.normalize());
								// arrow.setLength(cur_pos.distanceTo(newTarget) - 5, 10, 5);
							// }
						//}
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

			// function constructArrowHelper(source, target) {
				// var origin = new THREE.Vector3(50, 100, 50);
				// var terminus = new THREE.Vector3(75, 75, 75);
				// var direction = new THREE.Vector3().subVectors(terminus, origin).normalize();
				// var distance = origin.distanceTo(terminus);
				// var arrow = new THREE.ArrowHelper(direction, origin, distance, 0x884400);
				// //arrows.push(arrow);
				// //console.log(distance)
				// //console.log(arrow);
				// scene.add(arrow);
			// }


			d3.selection.prototype.moveToFront = function() {
				return this.each(function() {
					this.parentNode.appendChild(this);
				});
			};

			d3.selection.prototype.first = function() {
				return d3.select(this[0][0]);
			};
		});
	}

});

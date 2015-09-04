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
		}, 600, "swing", function() {
		});
	}
	
	
	if ($.cookie("depth") != "" && $.cookie("relations") != "")
		visualize("TZW:gezicht", $.cookie("depth"), $.cookie("relations"));
	else
		visualize("TZW:gezicht");

	function visualize(concept, depth, relations) {
		// Set visualisation variables
		var WIDTH = 1000;
		HEIGHT = 500;
		COLOR = "steelblue";
		LINK_COLOR = "#cccccc";

		// Create scene
		var scene = new THREE.Scene();

		// Set camera attributes and create camera
		var VIEW_ANGLE = 45,
		    ASPECT = WIDTH / HEIGHT,
		    NEAR = 0.1,
		    FAR = 10000;
		var camera = new THREE.PerspectiveCamera(VIEW_ANGLE, ASPECT, NEAR, FAR);
		scene.add(camera);
		camera.position.z = 300;

		// Set variable for container
		var $container = $('#canvas-svg');

		// Create Renderer
		var renderer = new THREE.WebGLRenderer({
			alpha : true,
			antialiasing : true
		});
		renderer.setClearColor(0x000000, 0);
		renderer.setSize(WIDTH, HEIGHT);

		// Create controls (orbitcontrols)
		var controls = new THREE.OrbitControls(camera);

		// Attach the render-supplied DOM element
		$container.append(renderer.domElement);

		// Create arrays for spheres and links
		var spheres = [], //Contains spheres
		    three_links = [];
		//Contains arrows
		labels = [];
		//Contains label sprites

		// Instantiate light sources
		var pointLight1 = new THREE.PointLight(0xFFFFFF);
		pointLight1.position.x = 0;
		pointLight1.position.y = 50;
		pointLight1.position.z = 500;
		scene.add(pointLight1);
		var pointLight2 = new THREE.PointLight(0xFFFFFF);
		pointLight2.position.x = 0;
		pointLight2.position.y = 500;
		pointLight2.position.z = -500;
		scene.add(pointLight2);
		var pointLight3 = new THREE.PointLight(0xFFFFFF);
		pointLight3.position.x = 500;
		pointLight3.position.y = 500;
		pointLight3.position.z = 0;
		scene.add(pointLight3);
		var pointLight4 = new THREE.PointLight(0xFFFFFF);
		pointLight4.position.x = -500;
		pointLight4.position.y = 50;
		pointLight4.position.z = 0;
		scene.add(pointLight4);
		var pointLight5 = new THREE.PointLight(0xFFFFFF);
		pointLight5.position.x = 0;
		pointLight5.position.y = -100;
		pointLight5.position.z = 0;
		scene.add(pointLight5);

		if ( typeof concept === 'undefined' || concept === '') {
			throw "Concept is undefined";
		}
		var depth = typeof depth !== 'undefined' ? depth : 1;
		var relations = typeof relations !== 'undefined' ? relations : "true,true";

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

			var nodelinks = JSON.parse(data);
			var nodes = [];

			// Compute the distinct nodes from the links.
			nodelinks.forEach(function(link) {
				link.source = nodes[link.source] || (nodes[link.source] = {
					name : link.source,
					url : link.urlsource
				});
				link.target = nodes[link.target] || (nodes[link.target] = {
					name : link.target,
					url : link.urltarget
				});
			});
  
			// User interaction
			window.addEventListener('resize', onWindowResize, false);

			visualize();
			animate();

			// Visualize RDF data
			function visualize() {

				// Create nodes and randomize default position
				for (var key in nodes) {
					if (nodes.hasOwnProperty(key)) {
						var val = nodes[key];
						nodes[key].x = Math.floor((Math.random() * 100) + 1);
						nodes[key].y = Math.floor((Math.random() * 100) + 1);
						nodes[key].z = Math.floor((Math.random() * 100) + 1);

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
						spheres[key] = sphere;

						// add the sphere to the scene
						scene.add(sphere);

						// Create label mesh
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

						labels[key] = mesh1;

						scene.add(mesh1);
					}
				}

				// Create arrows
				for (var i = 0; i < nodelinks.length; i++) {
					var origin = new THREE.Vector3(50, 100, 50);
					var terminus = new THREE.Vector3(75, 75, 75);
					var direction = new THREE.Vector3().subVectors(terminus, origin).normalize();
					var distance = origin.distanceTo(terminus);
					var arrow = new THREE.ArrowHelper(direction, origin, distance, 0x000000);
					arrow.userData = {
						target : nodes[nodelinks[i].target.name].name,
						source : nodes[nodelinks[i].source.name].name
					};
					scene.add(arrow);
					three_links.push(arrow);

				}
				initCola();
			}

			// Initializes force3D calculations and spaces nodes according to a forced layout
			function initCola() {
				// set up the axes
				var x = d3.scale.linear().domain([0, 350]).range([0, 10]),
				    y = d3.scale.linear().domain([0, 350]).range([0, 10]),
				    z = d3.scale.linear().domain([0, 350]).range([0, 10]);

				//.on("tick", function(e) {
				for (var key in nodes) {
					spheres[key].position.set(x(nodes[key].x) * 40 - 40, y(nodes[key].y) * 40 - 40, z(nodes[key].z) * 40 - 40);
					labels[key].position.set(x(nodes[key].x) * 40 - 40, y(nodes[key].y) * 40 - 40, z(nodes[key].z) * 40 - 40);

					for (var j = 0; j < three_links.length; j++) {
						var arrow = three_links[j];
						var vi = null;
						if (arrow.userData.source === key) {
							vi = 0;
						}
						if (arrow.userData.target === key) {
							vi = 1;
						}
						if (vi >= 0) {
							if (vi == 0) {
								var vectOrigin = new THREE.Vector3(spheres[key].position.x, spheres[key].position.y, spheres[key].position.z);
								setArrowOrigin(arrow, vectOrigin);
							}
							if (vi == 1) {
								var vectTarget = new THREE.Vector3(spheres[key].position.x, spheres[key].position.y, spheres[key].position.z);
								setArrowTarget(arrow, vectTarget);
							}
						}
					}

				}
				renderer.render(scene, camera);
			}

			// Animate the webGL objects for rendering
			function animate() {
				requestAnimationFrame(animate);
				renderer.render(scene, camera);
				controls.update();

				for (var label in labels) {
					labels[label].lookAt(camera.position);
				}
				render();
			}

			// Extension of default render function
			function render() {
			}

			// Actionlistener for resizing parent frame
			function onWindowResize(e) {
				WIDTH = $(window).width();
				HEIGHT = $(window).height();
				renderer.setSize(WIDTH, HEIGHT);
				camera.aspect = WIDTH / HEIGHT;
				camera.updateProjectionMatrix();
			}

			// Construction method for arrowhelpers
			function constructArrowHelper(source, target) {
				// Instantiate origin and target in Vector3 format
				var origin = new THREE.Vector3(10, 10, 10);
				var terminus = new THREE.Vector3(0, 0, 0);
				
				// Calculate terminus vectors
				var direction = new THREE.Vector3().subVectors(terminus, origin).normalize();
				var distance = origin.distanceTo(terminus);
				var arrow = new THREE.ArrowHelper(direction, origin, distance, 0x000000);

				// Set node data associated with the arrow
				arrow.userData = {
					target : nodes[nodelinks[i].target.name].name,
					source : nodes[nodelinks[i].source.name].name
				};
				scene.add(arrow);
			}

			function setArrowOrigin(arrow, origin) {
				//Get current position from sphere array
				vectTarget = spheres[arrow.userData.target].position;

				// Set arrow origin 
				arrow.position.x = origin.x;
				arrow.position.y = origin.y;
				arrow.position.z = origin.z;

				// Calculate new terminus vectors and set length
				arrow.setLength(arrow.position.distanceTo(vectTarget) - 5, 10, 5);
				arrow.setDirection(new THREE.Vector3().subVectors(vectTarget, arrow.position).normalize());
			}

			function setArrowTarget(arrow, target) {
				// Cast function argument to Vector3 format
				var newTarget = new THREE.Vector3(target.x, target.y, target.z);
				
				//Calculate new terminus vectors and set length
				arrow.setLength(arrow.position.distanceTo(newTarget) - 5, 10, 5);
				arrow.setDirection(new THREE.Vector3().subVectors(newTarget, arrow.position).normalize());
			}
			
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

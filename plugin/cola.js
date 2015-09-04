    var colaGraph = new tree3d.Graph(colaObject, n, graph.links, nodeColourings);
    var layout = new cola.Layout3D(graph.nodes, graph.links, 4);
    graph.constraints.forEach(function (c) {
        var r = c.right;
        c.right = c.left;
        c.left = r;
        c.gap *= 0.2;
    });
    layout.constraints = graph.constraints;
    layout.start(10);
    var render = function () {
        xAngle += mouse.dx / 100;
        yAngle += mouse.dy / 100;
        colaObject.rotation.set(yAngle, xAngle, 0);
        var s = converged ? 0 : layout.tick();
        if (s != 0 && Math.abs(Math.abs(delta / s) - 1) > 1e-7) {
            delta = s;
            colaGraph.setNodePositions(layout.result);
            colaGraph.update(); // Update all the edge positions
        }
        else {
            converged = true;
        }
        renderer.render(scene, camera);
        requestAnimationFrame(render);
    };
    render();
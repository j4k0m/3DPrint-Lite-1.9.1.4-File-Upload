var THREEx	= THREEx || {}

THREEx.GeometricGlowMesh	= function(mesh, in_distance, out_distance){
	var object3d	= new THREEP3DL.Object3D

	var geometry	= mesh.geometry.clone()
	THREEx.dilateGeometry(geometry, in_distance)
	var material	= THREEx.createAtmosphereMaterial()
	material.uniforms.glowColor.value	= new THREEP3DL.Color('cyan')
	material.uniforms.coeficient.value	= 1.1
	material.uniforms.power.value		= 1.4
	var insideMesh	= new THREEP3DL.Mesh(geometry, material );
	object3d.add( insideMesh );


	var geometry	= mesh.geometry.clone()
	THREEx.dilateGeometry(geometry, out_distance)
	var material	= THREEx.createAtmosphereMaterial()
	material.uniforms.glowColor.value	= new THREEP3DL.Color('cyan')
	material.uniforms.coeficient.value	= 0.1
	material.uniforms.power.value		= 1.2
	material.side	= THREEP3DL.BackSide
	var outsideMesh	= new THREEP3DL.Mesh( geometry, material );
	object3d.add( outsideMesh );

	// expose a few variable
	this.object3d	= object3d
	this.insideMesh	= insideMesh
	this.outsideMesh= outsideMesh
}

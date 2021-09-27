/**
 * @author Slayvin / http://slayvin.net
 */

THREEP3DL.ShaderLib[ 'mirror' ] = {

	uniforms: {
		"mirrorColor": { value: new THREEP3DL.Color( 0x7F7F7F ) },
		"mirrorSampler": { value: null },
		"textureMatrix" : { value: new THREEP3DL.Matrix4() }
	},

	vertexShader: [

		"uniform mat4 textureMatrix;",

		"varying vec4 mirrorCoord;",

		"void main() {",

			"vec4 mvPosition = modelViewMatrix * vec4( position, 1.0 );",
			"vec4 worldPosition = modelMatrix * vec4( position, 1.0 );",
			"mirrorCoord = textureMatrix * worldPosition;",

			"gl_Position = projectionMatrix * mvPosition;",

		"}"

	].join( "\n" ),

	fragmentShader: [

		"uniform vec3 mirrorColor;",
		"uniform sampler2D mirrorSampler;",

		"varying vec4 mirrorCoord;",

		"float blendOverlay(float base, float blend) {",
			"return( base < 0.5 ? ( 2.0 * base * blend ) : (1.0 - 2.0 * ( 1.0 - base ) * ( 1.0 - blend ) ) );",
		"}",

		"void main() {",

			"vec4 color = texture2DProj(mirrorSampler, mirrorCoord);",
			"color = vec4(blendOverlay(mirrorColor.r, color.r), blendOverlay(mirrorColor.g, color.g), blendOverlay(mirrorColor.b, color.b), 1.0);",

			"gl_FragColor = color;",

		"}"

	].join( "\n" )

};

THREEP3DL.Mirror = function ( renderer, camera, options ) {

	THREEP3DL.Object3D.call( this );

	this.name = 'mirror_' + this.id;

	options = options || {};

	this.matrixNeedsUpdate = true;

	var width = options.textureWidth !== undefined ? options.textureWidth : 512;
	var height = options.textureHeight !== undefined ? options.textureHeight : 512;

	this.clipBias = options.clipBias !== undefined ? options.clipBias : 0.0;

	var mirrorColor = options.color !== undefined ? new THREEP3DL.Color( options.color ) : new THREEP3DL.Color( 0x7F7F7F );

	this.renderer = renderer;
	this.mirrorPlane = new THREEP3DL.Plane();
	this.normal = new THREEP3DL.Vector3( 0, 0, 1 );
	this.mirrorWorldPosition = new THREEP3DL.Vector3();
	this.cameraWorldPosition = new THREEP3DL.Vector3();
	this.rotationMatrix = new THREEP3DL.Matrix4();
	this.lookAtPosition = new THREEP3DL.Vector3( 0, 0, - 1 );
	this.clipPlane = new THREEP3DL.Vector4();

	// For debug only, show the normal and plane of the mirror
	var debugMode = options.debugMode !== undefined ? options.debugMode : false;

	if ( debugMode ) {

		var arrow = new THREEP3DL.ArrowHelper( new THREEP3DL.Vector3( 0, 0, 1 ), new THREEP3DL.Vector3( 0, 0, 0 ), 10, 0xffff80 );
		var planeGeometry = new THREEP3DL.Geometry();
		planeGeometry.vertices.push( new THREEP3DL.Vector3( - 10, - 10, 0 ) );
		planeGeometry.vertices.push( new THREEP3DL.Vector3( 10, - 10, 0 ) );
		planeGeometry.vertices.push( new THREEP3DL.Vector3( 10, 10, 0 ) );
		planeGeometry.vertices.push( new THREEP3DL.Vector3( - 10, 10, 0 ) );
		planeGeometry.vertices.push( planeGeometry.vertices[ 0 ] );
		var plane = new THREEP3DL.Line( planeGeometry, new THREEP3DL.LineBasicMaterial( { color: 0xffff80 } ) );

		this.add( arrow );
		this.add( plane );

	}

	if ( camera instanceof THREEP3DL.PerspectiveCamera ) {

		this.camera = camera;

	} else {

		this.camera = new THREEP3DL.PerspectiveCamera();
		console.log( this.name + ': camera is not a Perspective Camera!' );

	}

	this.textureMatrix = new THREEP3DL.Matrix4();

	this.mirrorCamera = this.camera.clone();
	this.mirrorCamera.matrixAutoUpdate = true;

	var parameters = { minFilter: THREEP3DL.LinearFilter, magFilter: THREEP3DL.LinearFilter, format: THREEP3DL.RGBFormat, stencilBuffer: false };

	this.renderTarget = new THREEP3DL.WebGLRenderTarget( width, height, parameters );
	this.renderTarget2 = new THREEP3DL.WebGLRenderTarget( width, height, parameters );

	var mirrorShader = THREEP3DL.ShaderLib[ "mirror" ];
	var mirrorUniforms = THREEP3DL.UniformsUtils.clone( mirrorShader.uniforms );

	this.material = new THREEP3DL.ShaderMaterial( {

		fragmentShader: mirrorShader.fragmentShader,
		vertexShader: mirrorShader.vertexShader,
		uniforms: mirrorUniforms

	} );

	this.material.uniforms.mirrorSampler.value = this.renderTarget.texture;
	this.material.uniforms.mirrorColor.value = mirrorColor;
	this.material.uniforms.textureMatrix.value = this.textureMatrix;

	if ( ! THREEP3DL.Math.isPowerOfTwo( width ) || ! THREEP3DL.Math.isPowerOfTwo( height ) ) {

		this.renderTarget.texture.generateMipmaps = false;
		this.renderTarget2.texture.generateMipmaps = false;

	}

	this.updateTextureMatrix();
	this.render();

};

THREEP3DL.Mirror.prototype = Object.create( THREEP3DL.Object3D.prototype );
THREEP3DL.Mirror.prototype.constructor = THREEP3DL.Mirror;

THREEP3DL.Mirror.prototype.renderWithMirror = function ( otherMirror ) {

	// update the mirror matrix to mirror the current view
	this.updateTextureMatrix();
	this.matrixNeedsUpdate = false;

	// set the camera of the other mirror so the mirrored view is the reference view
	var tempCamera = otherMirror.camera;
	otherMirror.camera = this.mirrorCamera;

	// render the other mirror in temp texture
	otherMirror.renderTemp();
	otherMirror.material.uniforms.mirrorSampler.value = otherMirror.renderTarget2.texture;

	// render the current mirror
	this.render();
	this.matrixNeedsUpdate = true;

	// restore material and camera of other mirror
	otherMirror.material.uniforms.mirrorSampler.value = otherMirror.renderTarget.texture;
	otherMirror.camera = tempCamera;

	// restore texture matrix of other mirror
	otherMirror.updateTextureMatrix();

};

THREEP3DL.Mirror.prototype.updateTextureMatrix = function () {

	this.updateMatrixWorld();
	this.camera.updateMatrixWorld();

	this.mirrorWorldPosition.setFromMatrixPosition( this.matrixWorld );
	this.cameraWorldPosition.setFromMatrixPosition( this.camera.matrixWorld );

	this.rotationMatrix.extractRotation( this.matrixWorld );

	this.normal.set( 0, 0, 1 );
	this.normal.applyMatrix4( this.rotationMatrix );

	var view = this.mirrorWorldPosition.clone().sub( this.cameraWorldPosition );
	view.reflect( this.normal ).negate();
	view.add( this.mirrorWorldPosition );

	this.rotationMatrix.extractRotation( this.camera.matrixWorld );

	this.lookAtPosition.set( 0, 0, - 1 );
	this.lookAtPosition.applyMatrix4( this.rotationMatrix );
	this.lookAtPosition.add( this.cameraWorldPosition );

	var target = this.mirrorWorldPosition.clone().sub( this.lookAtPosition );
	target.reflect( this.normal ).negate();
	target.add( this.mirrorWorldPosition );

	this.up.set( 0, - 1, 0 );
	this.up.applyMatrix4( this.rotationMatrix );
	this.up.reflect( this.normal ).negate();

	this.mirrorCamera.position.copy( view );
	this.mirrorCamera.up = this.up;
	this.mirrorCamera.lookAt( target );

	this.mirrorCamera.updateProjectionMatrix();
	this.mirrorCamera.updateMatrixWorld();
	this.mirrorCamera.matrixWorldInverse.getInverse( this.mirrorCamera.matrixWorld );

	// Update the texture matrix
	this.textureMatrix.set( 0.5, 0.0, 0.0, 0.5,
							0.0, 0.5, 0.0, 0.5,
							0.0, 0.0, 0.5, 0.5,
							0.0, 0.0, 0.0, 1.0 );
	this.textureMatrix.multiply( this.mirrorCamera.projectionMatrix );
	this.textureMatrix.multiply( this.mirrorCamera.matrixWorldInverse );

	// Now update projection matrix with new clip plane, implementing code from: http://www.terathon.com/code/oblique.html
	// Paper explaining this technique: http://www.terathon.com/lengyel/Lengyel-Oblique.pdf
	this.mirrorPlane.setFromNormalAndCoplanarPoint( this.normal, this.mirrorWorldPosition );
	this.mirrorPlane.applyMatrix4( this.mirrorCamera.matrixWorldInverse );

	this.clipPlane.set( this.mirrorPlane.normal.x, this.mirrorPlane.normal.y, this.mirrorPlane.normal.z, this.mirrorPlane.constant );

	var q = new THREEP3DL.Vector4();
	var projectionMatrix = this.mirrorCamera.projectionMatrix;

	q.x = ( Math.sign( this.clipPlane.x ) + projectionMatrix.elements[ 8 ] ) / projectionMatrix.elements[ 0 ];
	q.y = ( Math.sign( this.clipPlane.y ) + projectionMatrix.elements[ 9 ] ) / projectionMatrix.elements[ 5 ];
	q.z = - 1.0;
	q.w = ( 1.0 + projectionMatrix.elements[ 10 ] ) / projectionMatrix.elements[ 14 ];

	// Calculate the scaled plane vector
	var c = new THREEP3DL.Vector4();
	c = this.clipPlane.multiplyScalar( 2.0 / this.clipPlane.dot( q ) );

	// Replacing the third row of the projection matrix
	projectionMatrix.elements[ 2 ] = c.x;
	projectionMatrix.elements[ 6 ] = c.y;
	projectionMatrix.elements[ 10 ] = c.z + 1.0 - this.clipBias;
	projectionMatrix.elements[ 14 ] = c.w;

};

THREEP3DL.Mirror.prototype.render = function () {

	if ( this.matrixNeedsUpdate ) this.updateTextureMatrix();

	this.matrixNeedsUpdate = true;

	// Render the mirrored view of the current scene into the target texture
	var scene = this;

	while ( scene.parent !== null ) {

		scene = scene.parent;

	}

	if ( scene !== undefined && scene instanceof THREEP3DL.Scene ) {

		// We can't render ourself to ourself
		var visible = this.material.visible;
		this.material.visible = false;

		this.renderer.render( scene, this.mirrorCamera, this.renderTarget, true );

		this.material.visible = visible;

	}

};

THREEP3DL.Mirror.prototype.renderTemp = function () {

	if ( this.matrixNeedsUpdate ) this.updateTextureMatrix();

	this.matrixNeedsUpdate = true;

	// Render the mirrored view of the current scene into the target texture
	var scene = this;

	while ( scene.parent !== null ) {

		scene = scene.parent;

	}

	if ( scene !== undefined && scene instanceof THREEP3DL.Scene ) {

		this.renderer.render( scene, this.mirrorCamera, this.renderTarget2, true );

	}

};

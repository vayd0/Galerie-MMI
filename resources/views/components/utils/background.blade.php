@if (!Request::is('/'))
<canvas class="z-0 opacity-[1]" id="sakura"></canvas>
<script id="sakura_point_vsh" type="x-shader/x_vertex">
uniform mat4 uProjection;
uniform mat4 uModelview;
uniform vec3 uResolution;
uniform vec3 uOffset;
uniform vec3 uDOF;
uniform vec3 uFade;
attribute vec3 aPosition;
attribute vec3 aEuler;
attribute vec2 aMisc;
varying vec3 pposition;
varying float psize;
varying float palpha;
varying float pdist;
varying vec3 normX;
varying vec3 normY;
varying vec3 normZ;
varying vec3 normal;
varying float diffuse;
varying float specular;
varying float rstop;
varying float distancefade;
void main(void) {
    vec4 pos = uModelview * vec4(aPosition + uOffset, 1.0);
    gl_Position = uProjection * pos;
    gl_PointSize = aMisc.x * uProjection[1][1] / -pos.z * uResolution.y * 0.5;
    pposition = pos.xyz;
    psize = aMisc.x;
    pdist = length(pos.xyz);
    palpha = smoothstep(0.0, 1.0, (pdist - 0.1) / uFade.z);
    vec3 elrsn = sin(aEuler);
    vec3 elrcs = cos(aEuler);
    mat3 rotx = mat3(
        1.0, 0.0, 0.0,
        0.0, elrcs.x, elrsn.x,
        0.0, -elrsn.x, elrcs.x
    );
    mat3 roty = mat3(
        elrcs.y, 0.0, -elrsn.y,
        0.0, 1.0, 0.0,
        elrsn.y, 0.0, elrcs.y
    );
    mat3 rotz = mat3(
        elrcs.z, elrsn.z, 0.0, 
        -elrsn.z, elrcs.z, 0.0,
        0.0, 0.0, 1.0
    );
    mat3 rotmat = rotx * roty * rotz;
    normal = rotmat[2];
    mat3 trrotm = mat3(
        rotmat[0][0], rotmat[1][0], rotmat[2][0],
        rotmat[0][1], rotmat[1][1], rotmat[2][1],
        rotmat[0][2], rotmat[1][2], rotmat[2][2]
    );
    normX = trrotm[0];
    normY = trrotm[1];
    normZ = trrotm[2];
    const vec3 lit = vec3(0.6917144638660746, 0.6917144638660746, -0.20751433915982237);
    float tmpdfs = dot(lit, normal);
    if(tmpdfs < 0.0) {
        normal = -normal;
        tmpdfs = dot(lit, normal);
    }
    diffuse = 0.4 + tmpdfs;
    vec3 eyev = normalize(-pos.xyz);
    if(dot(eyev, normal) > 0.0) {
        vec3 hv = normalize(eyev + lit);
        specular = pow(max(dot(hv, normal), 0.0), 20.0);
    }
    else {
        specular = 0.0;
    }
    rstop = clamp((abs(pdist - uDOF.x) - uDOF.y) / uDOF.z, 0.0, 1.0);
    rstop = pow(rstop, 0.5);
    distancefade = min(1.0, exp((uFade.x - pdist) * 0.69315 / uFade.y));
}
</script>
<script id="sakura_point_fsh" type="x-shader/x_fragment">
#ifdef GL_ES
precision highp float;
#endif
uniform vec3 uDOF;
uniform vec3 uFade;
const vec3 fadeCol = vec3(0.08, 0.03, 0.06);
varying vec3 pposition;
varying float psize;
varying float palpha;
varying float pdist;
varying vec3 normX;
varying vec3 normY;
varying vec3 normZ;
varying vec3 normal;
varying float diffuse;
varying float specular;
varying float rstop;
varying float distancefade;
float ellipse(vec2 p, vec2 o, vec2 r) {
    vec2 lp = (p - o) / r;
    return length(lp) - 1.0;
}
void main(void) {
    vec3 p = vec3(gl_PointCoord - vec2(0.5, 0.5), 0.0) * 2.0;
    vec3 d = vec3(0.0, 0.0, -1.0);
    float nd = normZ.z;
    if(abs(nd) < 0.0001) discard;
    float np = dot(normZ, p);
    vec3 tp = p + d * np / nd;
    vec2 coord = vec2(dot(normX, tp), dot(normY, tp));
    const float flwrsn = 0.258819045102521;
    const float flwrcs = 0.965925826289068;
    mat2 flwrm = mat2(flwrcs, -flwrsn, flwrsn, flwrcs);
    vec2 flwrp = vec2(abs(coord.x), coord.y) * flwrm;
    float r;
    if(flwrp.x < 0.0) {
        r = ellipse(flwrp, vec2(0.065, 0.024) * 0.5, vec2(0.36, 0.96) * 0.5);
    }
    else {
        r = ellipse(flwrp, vec2(0.065, 0.024) * 0.5, vec2(0.58, 0.96) * 0.5);
    }
    if(r > rstop) discard;
    vec3 col = mix(vec3(0.4, 0.7, 1.0), vec3(0.6, 0.8, 1.0), r);
    float grady = mix(0.0, 1.0, pow(coord.y * 0.5 + 0.5, 0.35));
    col *= vec3(1.0, grady, grady);
    col *= mix(0.8, 1.0, pow(abs(coord.x), 0.3));
    col = col * diffuse + specular;
    col = mix(fadeCol, col, distancefade);
    float alpha = (rstop > 0.001)? (0.5 - r / (rstop * 2.0)) : 1.0;
    alpha = smoothstep(0.0, 1.0, alpha) * palpha;
    gl_FragColor = vec4(col * 0.5, alpha);
}
</script>
<script id="fx_common_vsh" type="x-shader/x_vertex">
uniform vec3 uResolution;
attribute vec2 aPosition;
varying vec2 texCoord;
varying vec2 screenCoord;
void main(void) {
    gl_Position = vec4(aPosition, 0.0, 1.0);
    texCoord = aPosition.xy * 0.5 + vec2(0.5, 0.5);
    screenCoord = aPosition.xy * vec2(uResolution.z, 1.0);
}
</script>
<script id="bg_fsh" type="x-shader/x_fragment">
#ifdef GL_ES
precision highp float;
#endif
uniform vec2 uTimes;
varying vec2 texCoord;
varying vec2 screenCoord;
void main(void) {
    vec3 col;
    float c;
    vec2 tmpv = texCoord * vec2(0.8, 1.0) - vec2(0.95, 1.0);
    c = exp(-pow(length(tmpv) * 1.8, 2.0));
    col = mix(vec3(0.02, 0.0, 0.03), vec3(0.96, 0.98, 1.0) * 1.5, c);
    gl_FragColor = vec4(col * 0.5, 1.0);
}
</script>
<script id="fx_brightbuf_fsh" type="x-shader/x_fragment">
#ifdef GL_ES
precision highp float;
#endif
uniform sampler2D uSrc;
uniform vec2 uDelta;
varying vec2 texCoord;
varying vec2 screenCoord;
void main(void) {
    vec4 col = texture2D(uSrc, texCoord);
    gl_FragColor = vec4(col.rgb * 2.0 - vec3(0.5), 1.0);
}
</script>
<script id="fx_dirblur_r4_fsh" type="x-shader/x_fragment">
#ifdef GL_ES
precision highp float;
#endif
uniform sampler2D uSrc;
uniform vec2 uDelta;
uniform vec4 uBlurDir;
varying vec2 texCoord;
varying vec2 screenCoord;
void main(void) {
    vec4 col = texture2D(uSrc, texCoord);
    col = col + texture2D(uSrc, texCoord + uBlurDir.xy * uDelta);
    col = col + texture2D(uSrc, texCoord - uBlurDir.xy * uDelta);
    col = col + texture2D(uSrc, texCoord + (uBlurDir.xy + uBlurDir.zw) * uDelta);
    col = col + texture2D(uSrc, texCoord - (uBlurDir.xy + uBlurDir.zw) * uDelta);
    gl_FragColor = col / 5.0;
}
</script>
<script id="fx_common_fsh" type="x-shader/x_fragment">
#ifdef GL_ES
precision highp float;
#endif
uniform sampler2D uSrc;
uniform vec2 uDelta;
varying vec2 texCoord;
varying vec2 screenCoord;
void main(void) {
    gl_FragColor = texture2D(uSrc, texCoord);
}
</script>
<script id="pp_final_vsh" type="x-shader/x_vertex">
uniform vec3 uResolution;
attribute vec2 aPosition;
varying vec2 texCoord;
varying vec2 screenCoord;
void main(void) {
    gl_Position = vec4(aPosition, 0.0, 1.0);
    texCoord = aPosition.xy * 0.5 + vec2(0.5, 0.5);
    screenCoord = aPosition.xy * vec2(uResolution.z, 1.0);
}
</script>
<script id="pp_final_fsh" type="x-shader/x_fragment">
#ifdef GL_ES
precision highp float;
#endif
uniform sampler2D uSrc;
uniform sampler2D uBloom;
uniform vec2 uDelta;
varying vec2 texCoord;
varying vec2 screenCoord;
void main(void) {
    vec4 srccol = texture2D(uSrc, texCoord) * 2.0;
    vec4 bloomcol = texture2D(uBloom, texCoord);
    vec4 col;
    col = srccol + bloomcol * (vec4(1.0) + srccol);
    col *= smoothstep(1.0, 0.0, pow(length((texCoord - vec2(0.5)) * 2.0), 1.2) * 0.5);
    col = pow(col, vec4(0.45454545454545));
    gl_FragColor = vec4(col.rgb, 1.0);
    gl_FragColor.a = 1.0;
}
</script>
<script>
  var Vector3 = {};
  var Matrix44 = {};
  Vector3.create = function (x, y, z) {
    return { 'x': x, 'y': y, 'z': z };
  };
  Vector3.dot = function (v0, v1) {
    return v0.x * v1.x + v0.y * v1.y + v0.z * v1.z;
  };
  Vector3.cross = function (v, v0, v1) {
    v.x = v0.y * v1.z - v0.z * v1.y;
    v.y = v0.z * v1.x - v0.x * v1.z;
    v.z = v0.x * v1.y - v0.y * v1.x;
  };
  Vector3.normalize = function (v) {
    var l = v.x * v.x + v.y * v.y + v.z * v.z;
    if (l > 0.00001) {
      l = 1.0 / Math.sqrt(l);
      v.x *= l;
      v.y *= l;
      v.z *= l;
    }
  };
  Vector3.arrayForm = function (v) {
    if (v.array) {
      v.array[0] = v.x;
      v.array[1] = v.y;
      v.array[2] = v.z;
    }
    else {
      v.array = new Float32Array([v.x, v.y, v.z]);
    }
    return v.array;
  };
  Matrix44.createIdentity = function () {
    return new Float32Array([1.0, 0.0, 0.0, 0.0, 0.0, 1.0, 0.0, 0.0, 0.0, 0.0, 1.0, 0.0, 0.0, 0.0, 0.0, 1.0]);
  };
  Matrix44.loadProjection = function (m, aspect, vdeg, near, far) {
    var h = near * Math.tan(vdeg * Math.PI / 180.0 * 0.5) * 2.0;
    var w = h * aspect;
    m[0] = 2.0 * near / w;
    m[1] = 0.0;
    m[2] = 0.0;
    m[3] = 0.0;
    m[4] = 0.0;
    m[5] = 2.0 * near / h;
    m[6] = 0.0;
    m[7] = 0.0;
    m[8] = 0.0;
    m[9] = 0.0;
    m[10] = -(far + near) / (far - near);
    m[11] = -1.0;
    m[12] = 0.0;
    m[13] = 0.0;
    m[14] = -2.0 * far * near / (far - near);
    m[15] = 0.0;
  };
  Matrix44.loadLookAt = function (m, vpos, vlook, vup) {
    var frontv = Vector3.create(vpos.x - vlook.x, vpos.y - vlook.y, vpos.z - vlook.z);
    Vector3.normalize(frontv);
    var sidev = Vector3.create(1.0, 0.0, 0.0);
    Vector3.cross(sidev, vup, frontv);
    Vector3.normalize(sidev);
    var topv = Vector3.create(1.0, 0.0, 0.0);
    Vector3.cross(topv, frontv, sidev);
    Vector3.normalize(topv);
    m[0] = sidev.x;
    m[1] = topv.x;
    m[2] = frontv.x;
    m[3] = 0.0;
    m[4] = sidev.y;
    m[5] = topv.y;
    m[6] = frontv.y;
    m[7] = 0.0;
    m[8] = sidev.z;
    m[9] = topv.z;
    m[10] = frontv.z;
    m[11] = 0.0;
    m[12] = -(vpos.x * m[0] + vpos.y * m[4] + vpos.z * m[8]);
    m[13] = -(vpos.x * m[1] + vpos.y * m[5] + vpos.z * m[9]);
    m[14] = -(vpos.x * m[2] + vpos.y * m[6] + vpos.z * m[10]);
    m[15] = 1.0;
  };
  var timeInfo = {
    'start': 0, 'prev': 0,
    'delta': 0, 'elapsed': 0
  };
  var gl;
  var renderSpec = {
    'width': 0,
    'height': 0,
    'aspect': 1,
    'array': new Float32Array(3),
    'halfWidth': 0,
    'halfHeight': 0,
    'halfArray': new Float32Array(3)
  };
  renderSpec.setSize = function (w, h) {
    renderSpec.width = w;
    renderSpec.height = h;
    renderSpec.aspect = renderSpec.width / renderSpec.height;
    renderSpec.array[0] = renderSpec.width;
    renderSpec.array[1] = renderSpec.height;
    renderSpec.array[2] = renderSpec.aspect;
    renderSpec.halfWidth = Math.floor(w / 2);
    renderSpec.halfHeight = Math.floor(h / 2);
    renderSpec.halfArray[0] = renderSpec.halfWidth;
    renderSpec.halfArray[1] = renderSpec.halfHeight;
    renderSpec.halfArray[2] = renderSpec.halfWidth / renderSpec.halfHeight;
  };
  function deleteRenderTarget(rt) {
    gl.deleteFramebuffer(rt.frameBuffer);
    gl.deleteRenderbuffer(rt.renderBuffer);
    gl.deleteTexture(rt.texture);
  }
  function createRenderTarget(w, h) {
    var ret = {
      'width': w,
      'height': h,
      'sizeArray': new Float32Array([w, h, w / h]),
      'dtxArray': new Float32Array([1.0 / w, 1.0 / h])
    };
    ret.frameBuffer = gl.createFramebuffer();
    ret.renderBuffer = gl.createRenderbuffer();
    ret.texture = gl.createTexture();
    gl.bindTexture(gl.TEXTURE_2D, ret.texture);
    gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, w, h, 0, gl.RGBA, gl.UNSIGNED_BYTE, null);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
    gl.bindFramebuffer(gl.FRAMEBUFFER, ret.frameBuffer);
    gl.framebufferTexture2D(gl.FRAMEBUFFER, gl.COLOR_ATTACHMENT0, gl.TEXTURE_2D, ret.texture, 0);
    gl.bindRenderbuffer(gl.RENDERBUFFER, ret.renderBuffer);
    gl.renderbufferStorage(gl.RENDERBUFFER, gl.DEPTH_COMPONENT16, w, h);
    gl.framebufferRenderbuffer(gl.FRAMEBUFFER, gl.DEPTH_ATTACHMENT, gl.RENDERBUFFER, ret.renderBuffer);
    gl.bindTexture(gl.TEXTURE_2D, null);
    gl.bindRenderbuffer(gl.RENDERBUFFER, null);
    gl.bindFramebuffer(gl.FRAMEBUFFER, null);
    return ret;
  }
  function compileShader(shtype, shsrc) {
    var retsh = gl.createShader(shtype);
    gl.shaderSource(retsh, shsrc);
    gl.compileShader(retsh);
    if (!gl.getShaderParameter(retsh, gl.COMPILE_STATUS)) {
      var errlog = gl.getShaderInfoLog(retsh);
      gl.deleteShader(retsh);
      console.error(errlog);
      return null;
    }
    return retsh;
  }
  function createShader(vtxsrc, frgsrc, uniformlist, attrlist) {
    var vsh = compileShader(gl.VERTEX_SHADER, vtxsrc);
    var fsh = compileShader(gl.FRAGMENT_SHADER, frgsrc);
    if (vsh == null || fsh == null) {
      return null;
    }
    var prog = gl.createProgram();
    gl.attachShader(prog, vsh);
    gl.attachShader(prog, fsh);
    gl.deleteShader(vsh);
    gl.deleteShader(fsh);
    gl.linkProgram(prog);
    if (!gl.getProgramParameter(prog, gl.LINK_STATUS)) {
      var errlog = gl.getProgramInfoLog(prog);
      console.error(errlog);
      return null;
    }
    if (uniformlist) {
      prog.uniforms = {};
      for (var i = 0; i < uniformlist.length; i++) {
        prog.uniforms[uniformlist[i]] = gl.getUniformLocation(prog, uniformlist[i]);
      }
    }
    if (attrlist) {
      prog.attributes = {};
      for (var i = 0; i < attrlist.length; i++) {
        var attr = attrlist[i];
        prog.attributes[attr] = gl.getAttribLocation(prog, attr);
      }
    }
    return prog;
  }
  function useShader(prog) {
    gl.useProgram(prog);
    for (var attr in prog.attributes) {
      gl.enableVertexAttribArray(prog.attributes[attr]);
    }
  }
  function unuseShader(prog) {
    for (var attr in prog.attributes) {
      gl.disableVertexAttribArray(prog.attributes[attr]);
    }
    gl.useProgram(null);
  }
  var projection = {
    'angle': 60,
    'nearfar': new Float32Array([0.1, 100.0]),
    'matrix': Matrix44.createIdentity()
  };
  var camera = {
    'position': Vector3.create(0, 0, 100),
    'lookat': Vector3.create(0, 0, 0),
    'up': Vector3.create(0, 1, 0),
    'dof': Vector3.create(10.0, 4.0, 8.0),
    'matrix': Matrix44.createIdentity()
  };
  var pointFlower = {};
  var meshFlower = {};
  var sceneStandBy = false;
  var BlossomParticle = function () {
    this.velocity = new Array(3);
    this.rotation = new Array(3);
    this.position = new Array(3);
    this.euler = new Array(3);
    this.size = 1.0;
    this.alpha = 1.0;
    this.zkey = 0.0;
  };
  BlossomParticle.prototype.setVelocity = function (vx, vy, vz) {
    this.velocity[0] = vx;
    this.velocity[1] = vy;
    this.velocity[2] = vz;
  };
  BlossomParticle.prototype.setRotation = function (rx, ry, rz) {
    this.rotation[0] = rx;
    this.rotation[1] = ry;
    this.rotation[2] = rz;
  };
  BlossomParticle.prototype.setPosition = function (nx, ny, nz) {
    this.position[0] = nx;
    this.position[1] = ny;
    this.position[2] = nz;
  };
  BlossomParticle.prototype.setEulerAngles = function (rx, ry, rz) {
    this.euler[0] = rx;
    this.euler[1] = ry;
    this.euler[2] = rz;
  };
  BlossomParticle.prototype.setSize = function (s) {
    this.size = s;
  };
  BlossomParticle.prototype.update = function (dt, et) {
    this.position[0] += this.velocity[0] * dt;
    this.position[1] += this.velocity[1] * dt;
    this.position[2] += this.velocity[2] * dt;
    this.euler[0] += this.rotation[0] * dt;
    this.euler[1] += this.rotation[1] * dt;
    this.euler[2] += this.rotation[2] * dt;
  };
  function createPointFlowers() {
    var prm = gl.getParameter(gl.ALIASED_POINT_SIZE_RANGE);
    renderSpec.pointSize = { 'min': prm[0], 'max': prm[1] };
    var vtxsrc = document.getElementById("sakura_point_vsh").textContent;
    var frgsrc = document.getElementById("sakura_point_fsh").textContent;
    pointFlower.program = createShader(
      vtxsrc, frgsrc,
      ['uProjection', 'uModelview', 'uResolution', 'uOffset', 'uDOF', 'uFade'],
      ['aPosition', 'aEuler', 'aMisc']
    );
    useShader(pointFlower.program);
    pointFlower.offset = new Float32Array([0.0, 0.0, 0.0]);
    pointFlower.fader = Vector3.create(0.0, 10.0, 0.0);
    pointFlower.numFlowers = 1600;
    pointFlower.particles = new Array(pointFlower.numFlowers);
    pointFlower.dataArray = new Float32Array(pointFlower.numFlowers * (3 + 3 + 2));
    pointFlower.positionArrayOffset = 0;
    pointFlower.eulerArrayOffset = pointFlower.numFlowers * 3;
    pointFlower.miscArrayOffset = pointFlower.numFlowers * 6;
    pointFlower.buffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, pointFlower.buffer);
    gl.bufferData(gl.ARRAY_BUFFER, pointFlower.dataArray, gl.DYNAMIC_DRAW);
    gl.bindBuffer(gl.ARRAY_BUFFER, null);
    unuseShader(pointFlower.program);
    for (var i = 0; i < pointFlower.numFlowers; i++) {
      pointFlower.particles[i] = new BlossomParticle();
    }
  }
  function initPointFlowers() {
    pointFlower.area = Vector3.create(20.0, 20.0, 20.0);
    pointFlower.area.x = pointFlower.area.y * renderSpec.aspect;
    pointFlower.fader.x = 10.0;
    pointFlower.fader.y = pointFlower.area.z;
    pointFlower.fader.z = 0.1;
    var PI2 = Math.PI * 2.0;
    var tmpv3 = Vector3.create(0, 0, 0);
    var tmpv = 0;
    var symmetryrand = function () { return (Math.random() * 2.0 - 1.0); };
    for (var i = 0; i < pointFlower.numFlowers; i++) {
      var tmpprtcl = pointFlower.particles[i];
      tmpv3.x = symmetryrand() * 0.3 + 0.8;
      tmpv3.y = symmetryrand() * 0.2 - 1.0;
      tmpv3.z = symmetryrand() * 0.3 + 0.5;
      Vector3.normalize(tmpv3);
      tmpv = 2.0 + Math.random() * 1.0;
      tmpprtcl.setVelocity(tmpv3.x * tmpv, tmpv3.y * tmpv, tmpv3.z * tmpv);
      tmpprtcl.setRotation(
        symmetryrand() * PI2 * 0.5,
        symmetryrand() * PI2 * 0.5,
        symmetryrand() * PI2 * 0.5
      );
      tmpprtcl.setPosition(
        symmetryrand() * pointFlower.area.x,
        symmetryrand() * pointFlower.area.y,
        symmetryrand() * pointFlower.area.z
      );
      tmpprtcl.setEulerAngles(
        Math.random() * Math.PI * 2.0,
        Math.random() * Math.PI * 2.0,
        Math.random() * Math.PI * 2.0
      );
      tmpprtcl.setSize(0.9 + Math.random() * 0.1);
    }
  }
  function renderPointFlowers() {
    var PI2 = Math.PI * 2.0;
    var limit = [pointFlower.area.x, pointFlower.area.y, pointFlower.area.z];
    var repeatPos = function (prt, cmp, limit) {
      if (Math.abs(prt.position[cmp]) - prt.size * 0.5 > limit) {
        if (prt.position[cmp] > 0) {
          prt.position[cmp] -= limit * 2.0;
        }
        else {
          prt.position[cmp] += limit * 2.0;
        }
      }
    };
    var repeatEuler = function (prt, cmp) {
      prt.euler[cmp] = prt.euler[cmp] % PI2;
      if (prt.euler[cmp] < 0.0) {
        prt.euler[cmp] += PI2;
      }
    };
    for (var i = 0; i < pointFlower.numFlowers; i++) {
      var prtcl = pointFlower.particles[i];
      prtcl.update(timeInfo.delta, timeInfo.elapsed);
      repeatPos(prtcl, 0, pointFlower.area.x);
      repeatPos(prtcl, 1, pointFlower.area.y);
      repeatPos(prtcl, 2, pointFlower.area.z);
      repeatEuler(prtcl, 0);
      repeatEuler(prtcl, 1);
      repeatEuler(prtcl, 2);
      prtcl.alpha = 1.0;
      prtcl.zkey = (camera.matrix[2] * prtcl.position[0]
        + camera.matrix[6] * prtcl.position[1]
        + camera.matrix[10] * prtcl.position[2]
        + camera.matrix[14]);
    }
    pointFlower.particles.sort(function (p0, p1) { return p0.zkey - p1.zkey; });
    var ipos = pointFlower.positionArrayOffset;
    var ieuler = pointFlower.eulerArrayOffset;
    var imisc = pointFlower.miscArrayOffset;
    for (var i = 0; i < pointFlower.numFlowers; i++) {
      var prtcl = pointFlower.particles[i];
      pointFlower.dataArray[ipos] = prtcl.position[0];
      pointFlower.dataArray[ipos + 1] = prtcl.position[1];
      pointFlower.dataArray[ipos + 2] = prtcl.position[2];
      ipos += 3;
      pointFlower.dataArray[ieuler] = prtcl.euler[0];
      pointFlower.dataArray[ieuler + 1] = prtcl.euler[1];
      pointFlower.dataArray[ieuler + 2] = prtcl.euler[2];
      ieuler += 3;
      pointFlower.dataArray[imisc] = prtcl.size;
      pointFlower.dataArray[imisc + 1] = prtcl.alpha;
      imisc += 2;
    }
    gl.enable(gl.BLEND);
    gl.blendFunc(gl.SRC_ALPHA, gl.ONE_MINUS_SRC_ALPHA);
    var prog = pointFlower.program;
    useShader(prog);
    gl.uniformMatrix4fv(prog.uniforms.uProjection, false, projection.matrix);
    gl.uniformMatrix4fv(prog.uniforms.uModelview, false, camera.matrix);
    gl.uniform3fv(prog.uniforms.uResolution, renderSpec.array);
    gl.uniform3fv(prog.uniforms.uDOF, Vector3.arrayForm(camera.dof));
    gl.uniform3fv(prog.uniforms.uFade, Vector3.arrayForm(pointFlower.fader));
    gl.bindBuffer(gl.ARRAY_BUFFER, pointFlower.buffer);
    gl.bufferData(gl.ARRAY_BUFFER, pointFlower.dataArray, gl.DYNAMIC_DRAW);
    gl.vertexAttribPointer(prog.attributes.aPosition, 3, gl.FLOAT, false, 0, pointFlower.positionArrayOffset * Float32Array.BYTES_PER_ELEMENT);
    gl.vertexAttribPointer(prog.attributes.aEuler, 3, gl.FLOAT, false, 0, pointFlower.eulerArrayOffset * Float32Array.BYTES_PER_ELEMENT);
    gl.vertexAttribPointer(prog.attributes.aMisc, 2, gl.FLOAT, false, 0, pointFlower.miscArrayOffset * Float32Array.BYTES_PER_ELEMENT);
    for (var i = 1; i < 2; i++) {
      var zpos = i * -2.0;
      pointFlower.offset[0] = pointFlower.area.x * -1.0;
      pointFlower.offset[1] = pointFlower.area.y * -1.0;
      pointFlower.offset[2] = pointFlower.area.z * zpos;
      gl.uniform3fv(prog.uniforms.uOffset, pointFlower.offset);
      gl.drawArrays(gl.POINT, 0, pointFlower.numFlowers);
      pointFlower.offset[0] = pointFlower.area.x * -1.0;
      pointFlower.offset[1] = pointFlower.area.y * 1.0;
      pointFlower.offset[2] = pointFlower.area.z * zpos;
      gl.uniform3fv(prog.uniforms.uOffset, pointFlower.offset);
      gl.drawArrays(gl.POINT, 0, pointFlower.numFlowers);
      pointFlower.offset[0] = pointFlower.area.x * 1.0;
      pointFlower.offset[1] = pointFlower.area.y * -1.0;
      pointFlower.offset[2] = pointFlower.area.z * zpos;
      gl.uniform3fv(prog.uniforms.uOffset, pointFlower.offset);
      gl.drawArrays(gl.POINT, 0, pointFlower.numFlowers);
      pointFlower.offset[0] = pointFlower.area.x * 1.0;
      pointFlower.offset[1] = pointFlower.area.y * 1.0;
      pointFlower.offset[2] = pointFlower.area.z * zpos;
      gl.uniform3fv(prog.uniforms.uOffset, pointFlower.offset);
      gl.drawArrays(gl.POINT, 0, pointFlower.numFlowers);
    }
    pointFlower.offset[0] = 0.0;
    pointFlower.offset[1] = 0.0;
    pointFlower.offset[2] = 0.0;
    gl.uniform3fv(prog.uniforms.uOffset, pointFlower.offset);
    gl.drawArrays(gl.POINT, 0, pointFlower.numFlowers);
    gl.bindBuffer(gl.ARRAY_BUFFER, null);
    unuseShader(prog);
    gl.enable(gl.DEPTH_TEST);
    gl.disable(gl.BLEND);
  }
  function createEffectProgram(vtxsrc, frgsrc, exunifs, exattrs) {
    var ret = {};
    var unifs = ['uResolution', 'uSrc', 'uDelta'];
    if (exunifs) {
      unifs = unifs.concat(exunifs);
    }
    var attrs = ['aPosition'];
    if (exattrs) {
      attrs = attrs.concat(exattrs);
    }
    ret.program = createShader(vtxsrc, frgsrc, unifs, attrs);
    useShader(ret.program);
    ret.dataArray = new Float32Array([
      -1.0, -1.0,
      1.0, -1.0,
      -1.0, 1.0,
      1.0, 1.0
    ]);
    ret.buffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, ret.buffer);
    gl.bufferData(gl.ARRAY_BUFFER, ret.dataArray, gl.STATIC_DRAW);
    gl.bindBuffer(gl.ARRAY_BUFFER, null);
    unuseShader(ret.program);
    return ret;
  }
  function useEffect(fxobj, srctex) {
    var prog = fxobj.program;
    useShader(prog);
    gl.uniform3fv(prog.uniforms.uResolution, renderSpec.array);
    if (srctex != null) {
      gl.uniform2fv(prog.uniforms.uDelta, srctex.dtxArray);
      gl.uniform1i(prog.uniforms.uSrc, 0);
      gl.activeTexture(gl.TEXTURE0);
      gl.bindTexture(gl.TEXTURE_2D, srctex.texture);
    }
  }
  function drawEffect(fxobj) {
    gl.bindBuffer(gl.ARRAY_BUFFER, fxobj.buffer);
    gl.vertexAttribPointer(fxobj.program.attributes.aPosition, 2, gl.FLOAT, false, 0, 0);
    gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
  }
  function unuseEffect(fxobj) {
    unuseShader(fxobj.program);
  }
  var effectLib = {};
  function createEffectLib() {
    var vtxsrc, frgsrc;
    var cmnvtxsrc = document.getElementById("fx_common_vsh").textContent;
    frgsrc = document.getElementById("bg_fsh").textContent;
    effectLib.sceneBg = createEffectProgram(cmnvtxsrc, frgsrc, ['uTimes'], null);
    frgsrc = document.getElementById("fx_brightbuf_fsh").textContent;
    effectLib.mkBrightBuf = createEffectProgram(cmnvtxsrc, frgsrc, null, null);
    frgsrc = document.getElementById("fx_dirblur_r4_fsh").textContent;
    effectLib.dirBlur = createEffectProgram(cmnvtxsrc, frgsrc, ['uBlurDir'], null);
    vtxsrc = document.getElementById("pp_final_vsh").textContent;
    frgsrc = document.getElementById("pp_final_fsh").textContent;
    effectLib.finalComp = createEffectProgram(vtxsrc, frgsrc, ['uBloom'], null);
  }
  function createBackground() {
  }
  function initBackground() {
  }
  function renderBackground() {
    gl.disable(gl.DEPTH_TEST);
    unuseEffect(effectLib.sceneBg);
  }
  var postProcess = {};
  function createPostProcess() {
  }
  function initPostProcess() {
  }
  function renderPostProcess() {
    gl.disable(gl.DEPTH_TEST);
    var bindRT = function (rt, isclear) {
      gl.bindFramebuffer(gl.FRAMEBUFFER, rt.frameBuffer);
      gl.viewport(0, 0, rt.width, rt.height);
      if (isclear) {
        gl.clearColor(0, 0, 0, 0);
        gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);
      }
    };
    bindRT(renderSpec.wHalfRT0, true);
    useEffect(effectLib.mkBrightBuf, renderSpec.mainRT);
    drawEffect(effectLib.mkBrightBuf);
    unuseEffect(effectLib.mkBrightBuf);
    for (var i = 0; i < 2; i++) {
      var p = 1.5 + 1 * i;
      var s = 2.0 + 1 * i;
      bindRT(renderSpec.wHalfRT1, true);
      useEffect(effectLib.dirBlur, renderSpec.wHalfRT0);
      gl.uniform4f(effectLib.dirBlur.program.uniforms.uBlurDir, p, 0.0, s, 0.0);
      drawEffect(effectLib.dirBlur);
      unuseEffect(effectLib.dirBlur);
      bindRT(renderSpec.wHalfRT0, true);
      useEffect(effectLib.dirBlur, renderSpec.wHalfRT1);
      gl.uniform4f(effectLib.dirBlur.program.uniforms.uBlurDir, 0.0, p, 0.0, s);
      drawEffect(effectLib.dirBlur);
      unuseEffect(effectLib.dirBlur);
    }
    gl.bindFramebuffer(gl.FRAMEBUFFER, null);
    gl.viewport(0, 0, renderSpec.width, renderSpec.height);
    gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);
    useEffect(effectLib.finalComp, renderSpec.mainRT);
    gl.uniform1i(effectLib.finalComp.program.uniforms.uBloom, 1);
    gl.activeTexture(gl.TEXTURE1);
    gl.bindTexture(gl.TEXTURE_2D, renderSpec.wHalfRT0.texture);
    drawEffect(effectLib.finalComp);
    unuseEffect(effectLib.finalComp);
    gl.enable(gl.DEPTH_TEST);
  }
  var SceneEnv = {};
  function createScene() {
    createEffectLib();
    createBackground();
    createPointFlowers();
    createPostProcess();
    sceneStandBy = true;
  }
  function initScene() {
    initBackground();
    initPointFlowers();
    initPostProcess();
    camera.position.z = pointFlower.area.z + projection.nearfar[0];
    projection.angle = Math.atan2(pointFlower.area.y, camera.position.z + pointFlower.area.z) * 180.0 / Math.PI * 2.0;
    Matrix44.loadProjection(projection.matrix, renderSpec.aspect, projection.angle, projection.nearfar[0], projection.nearfar[1]);
  }
  function renderScene() {
    Matrix44.loadLookAt(camera.matrix, camera.position, camera.lookat, camera.up);
    gl.enable(gl.DEPTH_TEST);
    gl.bindFramebuffer(gl.FRAMEBUFFER, renderSpec.mainRT.frameBuffer);
    gl.viewport(0, 0, renderSpec.mainRT.width, renderSpec.mainRT.height);
    gl.clearColor(0.5, 0.7, 1.0, 1.0);
    gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);
    renderBackground();
    renderPointFlowers();
    renderPostProcess();
  }
  function onResize(e) {
    makeCanvasFullScreen(document.getElementById("sakura"));
    setViewports();
    if (sceneStandBy) {
      initScene();
    }
  }
  function setViewports() {
    renderSpec.setSize(gl.canvas.width, gl.canvas.height);
    gl.clearColor(0.2, 0.2, 0.5, 1.0);
    gl.viewport(0, 0, renderSpec.width, renderSpec.height);
    var rtfunc = function (rtname, rtw, rth) {
      var rt = renderSpec[rtname];
      if (rt) deleteRenderTarget(rt);
      renderSpec[rtname] = createRenderTarget(rtw, rth);
    };
    rtfunc('mainRT', renderSpec.width, renderSpec.height);
    rtfunc('wFullRT0', renderSpec.width, renderSpec.height);
    rtfunc('wFullRT1', renderSpec.width, renderSpec.height);
    rtfunc('wHalfRT0', renderSpec.halfWidth, renderSpec.halfHeight);
    rtfunc('wHalfRT1', renderSpec.halfWidth, renderSpec.halfHeight);
  }
  function render() {
    renderScene();
  }
  var animating = true;
  function toggleAnimation(elm) {
    animating ^= true;
    if (animating) animate();
    if (elm) {
      elm.innerHTML = animating ? "Stop" : "Start";
    }
  }
  function stepAnimation() {
    if (!animating) animate();
  }
  function animate() {
    var curdate = new Date();
    timeInfo.elapsed = (curdate - timeInfo.start) / 1000.0;
    timeInfo.delta = (curdate - timeInfo.prev) / 1000.0;
    timeInfo.prev = curdate;
    if (animating) requestAnimationFrame(animate);
    render();
  }
  function makeCanvasFullScreen(canvas) {
    var b = document.body;
    var d = document.documentElement;
    fullw = Math.max(b.clientWidth, b.scrollWidth, d.scrollWidth, d.clientWidth);
    fullh = Math.max(b.clientHeight, b.scrollHeight, d.scrollHeight, d.clientHeight);
    canvas.width = fullw;
    canvas.height = fullh;
  }
  window.addEventListener('load', function (e) {
    var canvas = document.getElementById("sakura");
    try {
      makeCanvasFullScreen(canvas);
      gl = canvas.getContext('experimental-webgl');
    } catch (e) {
      alert("WebGL not supported." + e);
      console.error(e);
      return;
    }
    window.addEventListener('resize', onResize);
    setViewports();
    createScene();
    initScene();
    timeInfo.start = new Date();
    timeInfo.prev = timeInfo.start;
    animate();
  });
  (function (w, r) {
    w['r' + r] = w['r' + r] || w['webkitR' + r] || w['mozR' + r] || w['msR' + r] || w['oR' + r] || function (c) { w.setTimeout(c, 1000 / 60); };
  })(window, 'requestAnimationFrame');
</script>
@else
<svg class="z-0 opacity-[1] min-w-screen overflow-y-scroll overflow-x-hidden"  id="Calque_1" data-name="Calque 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 3172.88 7810.66">
  <defs>
    <style>
      .cls-1 {
        fill: #3c3c3b;
      }

      .cls-2 {
        fill: #554738;
      }

      .cls-3 {
        fill: #f6d7b6;
      }

      .cls-4 {
        fill: #051d1b;
      }

      .cls-5 {
        stroke-width: .5px;
      }

      .cls-5, .cls-6, .cls-7 {
        fill: #1d1d1b;
      }

      .cls-5, .cls-8, .cls-7 {
        stroke-miterlimit: 10;
      }

      .cls-5, .cls-7 {
        stroke: #1d1d1b;
      }

      .cls-9 {
        fill: #459eb0;
      }

      .cls-10 {
        fill: #13678a;
      }

      .cls-11 {
        fill: #faff60;
      }

      .cls-8 {
        stroke: #000;
      }

      .cls-8, .cls-12 {
        fill: #fff;
      }

      .cls-13 {
        fill: #575756;
      }

      .cls-14 {
        fill: #706f6f;
      }

      .cls-15 {
        fill: #26b99a;
      }

      .cls-16 {
        fill: #3c2d1e;
      }

      .cls-17 {
        fill: #878787;
      }

      .cls-18 {
        fill: #45c4b0;
      }

      .cls-19 {
        fill: #3c2d1b;
      }

      .cls-20 {
        fill: #dafdba;
      }

      .cls-21 {
        fill: #faff3e;
      }

      .cls-22 {
        fill: #faff6a;
        opacity: .65;
      }

      .cls-23 {
        fill: #81ccb8;
      }

      .cls-24 {
        fill: #e8c299;
      }

      .cls-25 {
        fill: #c9a177;
      }
    </style>
  </defs>
  <rect class="cls-4" x="0" width="3173.86" height="1800.17"/>
  <g id="SQUISH1">
    <path class="cls-20" d="M1931.89,370.81c7.28,20.94,18.71,64.31,0,106.01-19.65,43.77-61.16,60.41-137.36,90.95-66.02,26.46-99.03,39.68-135.78,26.3-46.59-16.97-68.21-63.78-75.38-81.62,47.17,38.93,78.83,41.29,98.7,36.75,41.12-9.38,41.16-50.4,91.69-71.97,49.52-21.14,78.78,5.76,114.49-17.22,28.53-18.36,39.57-54.64,43.64-89.2Z"/>
    <path class="cls-18" d="M1891.75,597.65c-3.47-2.96-9.14-7.41-16.78-11.58-40.69-22.19-79.51-2.19-108.03-6.88-29.07-4.78-68.16-27.56-108.18-110.64,20.31-7.75,88.4-30.78,156.07.12,17.37,7.93,47.6,21.73,65.49,55.8,16.1,30.65,13.31,60.76,11.43,73.19Z"/>
    <path class="cls-10" d="M1877.53,410.34c5.82,103.72-18.05,141.66-41.92,156.69-21.16,13.32-52.8,8.41-78.89,35.55-13.18,13.72-19.33,29.25-22.4,39.47-8.93-7.8-24.4-23.76-29.82-47.78-9.08-40.26,16.56-74.44,29.05-91.07,21.04-28.04,45.95-41.84,83.39-63.51,24.22-14.02,45.56-23.39,60.6-29.35Z"/>
    <path class="cls-12" d="M1861.2,515.33c-3.7,6.58-23.62,40.4-66.67,52.43-42.93,12-77.42-6.46-84.03-10.17,1.32-4.11,16-47.29,61.43-61.37,48.45-15.02,86.84,17.02,89.27,19.11Z"/>
    <ellipse class="cls-7" cx="1788.2" cy="528.9" rx="20.52" ry="20.73" transform="translate(-76.43 505.21) rotate(-15.75)"/>
    <path class="cls-20" d="M1753.69,488.9c6.92-4.52,26.6-16.94,54.03-14.81,32.65,2.54,52.77,23.85,57.34,28.95-1.29,4.09-2.58,8.19-3.87,12.28-6.16-4.46-15.12-10.08-26.74-14.65-7.64-3.01-20.92-8.23-37.44-8.11-29.49.21-50.34,17.31-57.03,22.96-17.61,14.86-25.82,32.38-29.48,42.07,1.32-7.79,8.65-46.12,43.19-68.69Z"/>
    <polygon class="cls-10" points="1637.73 572.98 1599.37 538.6 1636.87 566.79 1637.73 572.98"/>
    <ellipse class="cls-12" cx="1797.54" cy="518.82" rx="4.98" ry="5.03" transform="translate(-73.34 507.37) rotate(-15.75)"/>
    <path class="cls-10" d="M1910.5,447.32c-.35-.36,6.04-5.74,11.69-15.05,4.06-6.69,5.1-11.08,5.32-10.99.35.14-2,11.99-10.78,21.13-2.99,3.12-6,5.15-6.23,4.92Z"/>
    <path class="cls-10" d="M1930.8,449.78c-.47-.16,2.74-7.87,3.49-18.74.54-7.82-.54-12.2-.31-12.22.37-.04,3.71,11.59.1,23.74-1.23,4.14-2.97,7.33-3.28,7.22Z"/>
    <path class="cls-10" d="M1640.57,565.77c-12.35-9.74-24.7-19.48-37.06-29.22,11.74,8.63,23.48,17.27,35.21,25.9.61,1.11,1.23,2.21,1.84,3.32Z"/>
  </g>
  <g id="SQUISH2">
    <path class="cls-20" d="M640.59,606.09c-12.03,4.85-37.06,12.76-61.96,3-26.14-10.24-37.04-34.04-57.04-77.71-17.33-37.84-26-56.76-19.21-78.61,8.6-27.71,35.35-41.67,45.57-46.36-21.42,28.67-21.9,47.24-18.69,58.73,6.65,23.77,30.62,22.63,44.66,51.55,13.76,28.34-1.14,46.2,13.31,66.42,11.54,16.15,33.06,21.58,53.37,22.98Z"/>
    <path class="cls-18" d="M506.88,589.05c1.63-2.11,4.07-5.55,6.29-10.14,11.82-24.41-.97-46.53.96-63.33,1.97-17.12,14.18-40.62,61.6-66.36,5.1,11.65,20.49,50.79,4.35,91.22-4.14,10.38-11.36,28.43-30.76,39.86-17.46,10.28-35.14,9.5-42.45,8.75Z"/>
    <path class="cls-10" d="M615.95,575.43c-60.46,6.34-83.3-6.54-92.76-20.06-8.38-11.99-6.41-30.62-23.01-45.1-8.39-7.32-17.64-10.47-23.7-11.98,4.31-5.44,13.19-14.94,27.08-18.78,23.27-6.45,43.98,7.57,54.05,14.4,16.98,11.5,25.76,25.67,39.48,46.94,8.88,13.76,14.96,25.96,18.87,34.59Z"/>
    <path class="cls-12" d="M554.12,568.86c-3.95-1.98-24.28-12.66-32.53-37.48-8.23-24.75,1.59-45.43,3.57-49.4,2.44.65,28.09,8.01,37.61,34.17,10.15,27.89-7.49,51.24-8.64,52.71Z"/>
    <ellipse class="cls-7" cx="544.13" cy="526.58" rx="12.13" ry="12.01" transform="translate(-139.09 200.13) rotate(-18.52)"/>
    <path class="cls-20" d="M566.52,505.28c2.84,3.92,10.65,15.07,10.18,31.16-.56,19.15-12.45,31.52-15.3,34.33-2.43-.64-4.86-1.28-7.29-1.92,2.43-3.73,5.46-9.12,7.81-16.04,1.54-4.55,4.22-12.46,3.68-22.11-.96-17.23-11.54-28.93-15.03-32.68-9.19-9.87-19.65-14.18-25.42-16.04,4.59.55,27.2,3.75,41.37,23.3Z"/>
    <polygon class="cls-10" points="514.1 439.89 533.11 416.5 517.69 439.21 514.1 439.89"/>
    <ellipse class="cls-12" cx="550.28" cy="531.75" rx="2.94" ry="2.91" transform="translate(-140.42 202.35) rotate(-18.52)"/>
    <path class="cls-10" d="M595.27,595.75c.2-.21,3.52,3.37,9.13,6.41,4.03,2.18,6.62,2.67,6.58,2.8-.07.21-7.06-.83-12.65-5.7-1.91-1.66-3.18-3.36-3.05-3.5Z"/>
    <path class="cls-10" d="M594.4,607.68c.08-.28,4.68,1.38,11.05,1.51,4.58.09,7.11-.66,7.13-.53.03.22-6.67,2.5-13.87.73-2.46-.6-4.37-1.53-4.31-1.71Z"/>
    <path class="cls-10" d="M518.4,441.35c5.34-7.49,10.69-14.99,16.03-22.48-4.71,7.1-9.43,14.21-14.14,21.31-.63.39-1.26.78-1.89,1.17Z"/>
  </g>
  <polyline class="cls-1" points="0 814.94 2.1 812.95 200.51 625.35 487.2 285.99 662.91 855 772.66 652.19 1009.51 361.33 1129.95 842.48 1388.89 602.23 1883.67 472.11 2110.25 375.65 2755.72 1018.4 2970.33 855 3173.86 1125.36 -2.13 1138.22"/>
  <path class="cls-6" d="M0,1951.97c-.71-271.25-1.42-542.5-2.13-813.75,347.35-94.4,694.69-188.81,1042.04-283.21,342.71-4.17,685.43-8.35,1028.14-12.52,368.6,94.29,737.21,188.59,1105.81,282.88-.33,275.54-.65,551.07-.98,826.61"/>
  <path class="cls-9" d="M462.16,1330.81c-31.23-97.51-346.69-55.88-352.87-104.82-7.4-58.54,435.43-184.87,960.43-250,516.25-64.05,1027.76-58.47,1045.01,36.62,11.38,62.73-205.86,89.72-268.4,266.25-72.52,204.71,146.69,374.27,109.3,488.19-47.7,145.35-510.01,190.77-1935.75-50.61,242.09-139.04,471.05-295.77,442.27-385.63Z"/>
  <path class="cls-10" d="M3192.77,1950.82c-81.52-34.42-169.1-82.12-217.84-145-127.7-164.75,95.07-325.53,77.12-328.54-14.28-2.4-141.14,101.63-544.55,473.79,5.67-41.97,10.73-99.66,8.07-168.2-10.98-283.3-143.2-481.06-154.65-480.33-11.44.73,75.86,201.08-12.05,402.8-14.97,34.35-43.77,88.82-138.16,145.32-86.63,51.86-191.52,83.29-260.13,100.61-23.72-23.32-49.72-54.53-65.08-92.23-93.61-229.85,323.41-419.16,296.6-426.02-23.12-5.91-354.21,129.4-487.69,345.2-23.12,37.37-37.29,72.28-45.75,103.62-10.11,37.47-9.67,69.56-11.1,69.54-1.09-.01,86.67-139.23-31.32-246.75-36.37-33.14-91.86-63.07-159.54-99.57-18.31-9.88-128.51-69.31-144.08-63.01-20.44,8.28,183.22,105.13,180.4,221.38-1.93,79.55-99.95,144.37-186.93,188.07-86.6-27.72-204.86-69.05-329.32-125.15-289.65-130.57-412.7-250.91-430.99-247.47-13.95,2.63,45.45,74.87,357.86,372.77-194.21-77.45-412.75-190.74-500.13-343-119.86-208.85,65.45-389.22,28.31-392.9-37.34-3.71-213.45,179.69-316.93,411.17-59.65,133.44-78.4,247.06-85.01,325.05"/>
  <path class="cls-14" d="M-7.66,1953.21c180.33-212.74,360.65-425.48,540.98-638.21v231.42c84.56-106.81,169.11-213.62,253.67-320.43,75.66,44.5,151.31,89.01,226.97,133.51,53.4,91.97,106.81,183.95,160.21,275.92l169.11,317.79H-7.66Z"/>
  <polygon class="cls-13" points="675.47 1545.09 816.9 1318.13 1003.81 1460.54 1164.03 1861.07 848.05 1407.13 675.47 1545.09"/>
  <polygon class="cls-13" points="109.29 1910.02 456.42 1522.84 282.86 1910.02 109.29 1910.02"/>
  <polygon class="cls-14" points="3173.86 1291.65 2445.42 744.3 2012.46 1596.52 2242.3 1960.13 3173.86 1960.13 3173.86 1291.65"/>
  <polygon class="cls-13" points="2512.44 878.07 3127.14 1339.52 3127.14 1522.84 2512.44 878.07"/>
  <polygon class="cls-13" points="2138.28 1562.25 2368.12 1910.02 2961.44 1910.02 2239.84 1412.59 2138.28 1562.25"/>
  <polygon class="cls-13" points="2031.38 1960.13 1827.88 1373.59 2464.34 744.3 2031.38 1596.52 2261.22 1960.13 2031.38 1960.13"/>
  <polygon class="cls-13" points="19.89 1788.55 540.98 1309.23 0 1951.97 19.89 1788.55"/>
  <polygon class="cls-13" points="794.65 1220.22 531.01 1471.43 531.01 1553.6 794.65 1220.22"/>
  <polygon class="cls-14" points="78.68 842.48 456.42 420.35 394.1 600.59 78.68 842.48"/>
  <polygon class="cls-14" points="1000.71 472.11 886.71 571.97 759.02 768.74 1000.71 472.11"/>
  <path class="cls-14" d="M2390.58,744.3l-273-303.08s192.7,337.15,202.34,327.52,70.66-24.44,70.66-24.44Z"/>
  <polygon class="cls-14" points="1282.53 768.94 1423.85 656.33 1902.4 524.65 1484.87 691.66 1282.53 768.94"/>
  <polygon class="cls-14" points="2953.68 899.84 3013.48 1045.44 3116.83 1094.85 2953.68 899.84"/>
  <polygon class="cls-14" points="2970.33 855 3138.25 1014.96 3173.86 1060.3 3173.86 1125.36 2970.33 855"/>
  <polygon class="cls-14" points="1129.95 842.48 1320.07 605.08 1438.83 537.74 2110.25 375.65 1883.67 472.11 1388.89 602.23 1129.95 842.48"/>
  <polygon class="cls-14" points="2110.25 375.65 2782.18 997.34 2576.08 842.48 2110.25 375.65"/>
  <polygon class="cls-14" points="662.91 855 733.04 631.42 1009.51 361.33 772.66 652.19 662.91 855"/>
  <polyline class="cls-14" points="1009.51 361.33 1129.95 744.3 1129.95 842.48"/>
  <path class="cls-17" d="M0,787v27.94c66.84-63.2,133.67-126.39,200.51-189.59l286.69-339.36-321.49,334.44L0,787Z"/>
  <path class="cls-20" d="M1274.18,848.95c-61.65,55.82-104.82,62.08-133.15,57.05-35.67-6.34-66.98-34.01-135.52-39.95-11.67-1.01-21.27-1.12-27.37-1.06,68.73-68.8,112.28-76.81,140.2-69,38.65,10.81,60.92,55.73,116.01,58.46,16.84.83,30.81-2.57,39.82-5.49Z"/>
  <path class="cls-18" d="M1199.22,943.92c-2.53-3.79-6.79-9.61-13.01-15.7-33.14-32.4-75.93-23.69-102.11-35.95-26.68-12.49-58.12-45.03-74.09-135.85,21.65-1.94,93.44-5.63,150.18,42.47,14.56,12.35,39.91,33.84,47.89,71.48,7.18,33.87-3.68,62.1-8.86,73.54Z"/>
  <path class="cls-10" d="M1236.38,759.78c-22.55,101.41-55.82,131.45-82.87,139.43-23.98,7.08-53.1-6.24-85.58,12.8-16.41,9.62-26.55,22.91-32.28,31.91-6.48-9.93-17.04-29.49-15.74-54.08,2.19-41.22,36.15-67.15,52.67-79.77,27.86-21.27,55.59-27.8,97.5-38.49,27.12-6.92,50.2-10.15,66.29-11.8Z"/>
  <path class="cls-12" d="M1192.16,856.4c-5.35,5.33-33.69,32.47-78.4,32.37-44.58-.1-72.76-27.23-78.11-32.6,2.38-3.6,28.23-41.17,75.78-42.4,50.7-1.3,78.97,39.95,80.73,42.62Z"/>
  <ellipse class="cls-7" cx="1118.23" cy="849.64" rx="20.52" ry="20.73"/>
  <path class="cls-20" d="M1095.86,801.78c7.89-2.47,30.2-9.08,56.03.42,30.73,11.3,44.32,37.28,47.33,43.43-2.35,3.59-4.71,7.18-7.06,10.77-4.72-5.96-11.81-13.8-21.76-21.36-6.54-4.97-17.9-13.6-33.83-17.97-28.44-7.8-53.14,2.99-61.12,6.62-20.98,9.53-33.64,24.15-39.8,32.49,3.38-7.13,20.84-42.05,60.21-54.39Z"/>
  <polygon class="cls-10" points="985.82 862.17 1025.16 828.91 992.07 862.17 985.82 862.17"/>
  <ellipse class="cls-12" cx="1129.95" cy="842.48" rx="4.98" ry="5.03"/>
  <path class="cls-10" d="M1235.94,856.4c.05-.5,8.29.84,19.03-.96,7.72-1.29,11.72-3.37,11.8-3.15.12.36-10.39,6.31-23.04,5.62-4.32-.23-7.82-1.18-7.79-1.51Z"/>
  <path class="cls-10" d="M1236.94,862.17c-.03-.5,8.32-.47,18.66-3.92,7.43-2.48,11.07-5.15,11.18-4.95.18.33-9.3,7.85-21.9,9.15-4.3.44-7.91.05-7.93-.28Z"/>
  <path class="cls-10" d="M992.91,863.59c11.16-11.09,22.32-22.17,33.48-33.26-9.99,10.61-19.97,21.22-29.96,31.83-1.17.48-2.35.95-3.52,1.43Z"/>
  <g>
    <path class="cls-20" d="M1369.31,826.08c61.65,55.82,104.82,62.08,133.15,57.05,35.67-6.34,66.98-34.01,135.52-39.95,11.67-1.01,21.27-1.12,27.37-1.06-68.73-68.8-112.28-76.81-140.2-69-38.65,10.81-60.92,55.73-116.01,58.46-16.84.83-30.81-2.57-39.82-5.49Z"/>
    <path class="cls-18" d="M1444.27,921.05c2.53-3.79,6.79-9.61,13.01-15.7,33.14-32.4,75.93-23.69,102.11-35.95,26.68-12.49,58.12-45.03,74.09-135.85-21.65-1.94-93.44-5.63-150.18,42.47-14.56,12.35-39.91,33.84-47.89,71.48-7.18,33.87,3.68,62.1,8.86,73.54Z"/>
    <path class="cls-10" d="M1407.11,736.91c22.55,101.41,55.82,131.45,82.87,139.43,23.98,7.08,53.1-6.24,85.58,12.8,16.41,9.62,26.55,22.91,32.28,31.91,6.48-9.93,17.04-29.49,15.74-54.08-2.19-41.22-36.15-67.15-52.67-79.77-27.86-21.27-55.59-27.8-97.5-38.49-27.12-6.92-50.2-10.15-66.29-11.8Z"/>
    <path class="cls-12" d="M1451.33,833.53c5.35,5.33,33.69,32.47,78.4,32.37,44.58-.1,72.76-27.23,78.11-32.6-2.38-3.6-28.23-41.17-75.78-42.4-50.7-1.3-78.97,39.95-80.73,42.62Z"/>
    <ellipse class="cls-7" cx="1525.27" cy="826.77" rx="20.52" ry="20.73"/>
    <path class="cls-20" d="M1547.63,778.91c-7.89-2.47-30.2-9.08-56.03.42-30.73,11.3-44.32,37.28-47.33,43.43,2.35,3.59,4.71,7.18,7.06,10.77,4.72-5.96,11.81-13.8,21.76-21.36,6.54-4.97,17.9-13.6,33.83-17.97,28.44-7.8,53.14,2.99,61.12,6.62,20.98,9.53,33.64,24.15,39.8,32.49-3.38-7.13-20.84-42.05-60.21-54.39Z"/>
    <polygon class="cls-10" points="1657.67 839.3 1618.34 806.04 1651.42 839.3 1657.67 839.3"/>
    <ellipse class="cls-12" cx="1513.55" cy="819.61" rx="4.98" ry="5.03"/>
    <path class="cls-10" d="M1407.55,833.53c-.05-.5-8.29.84-19.03-.96-7.72-1.29-11.72-3.37-11.8-3.15-.12.36,10.39,6.31,23.04,5.62,4.32-.23,7.82-1.18,7.79-1.51Z"/>
    <path class="cls-10" d="M1406.55,839.3c.03-.5-8.32-.47-18.66-3.92-7.43-2.48-11.07-5.15-11.18-4.95-.18.33,9.3,7.85,21.9,9.15,4.3.44,7.91.05,7.93-.28Z"/>
    <path class="cls-10" d="M1650.58,840.72c-11.16-11.09-22.32-22.17-33.48-33.26,9.99,10.61,19.97,21.22,29.96,31.83,1.17.48,2.35.95,3.52,1.43Z"/>
  </g>
  <g>
    <path class="cls-20" d="M1053.68,1366.34c-52.99-23.43-87.43-50.92-109.15-71.8-48.86-46.97-64.54-83.62-109.79-97.18-33.44-10.02-65.7-1.53-87.46,7.02,73.74-48.18,161.26-55.11,219.97-16.05,49.68,33.05,71.12,93.95,75.09,106,9.64,29.26,11.39,54.98,11.33,72Z"/>
    <path class="cls-18" d="M969.38,1356.91c-.46-4.54-1.49-11.67-4.14-19.96-14.09-44.15-55.98-56.51-73.36-79.6-17.71-23.54-30.25-67.01-1.8-154.73,20.04,8.43,85.18,38.81,112.77,107.89,7.08,17.73,19.41,48.6,8.81,85.59-9.53,33.28-32.35,53.13-42.29,60.82Z"/>
    <path class="cls-10" d="M1088.49,1211.65c-67.44,79.02-110.91,89.97-138.54,84.34-24.5-4.99-43.99-30.4-81.6-28.79-19.01.81-34.19,7.8-43.47,13.07-1.07-11.81-1.24-34.04,11.44-55.15,21.24-35.39,63.4-42.39,83.91-45.79,34.58-5.74,62.13,1.49,104.17,11.68,27.2,6.6,49.1,14.56,64.09,20.64Z"/>
    <path class="cls-12" d="M1004.15,1276.29c-7.22,2.2-44.98,12.9-84.43-8.14-39.33-20.98-51.51-58.15-53.73-65.4,3.79-2.06,44.24-23.14,86.81-1.94,45.4,22.61,51.04,72.29,51.34,75.48Z"/>
    <ellipse class="cls-7" cx="942" cy="1235.68" rx="20.73" ry="20.52" transform="translate(-591.02 1488.85) rotate(-62.06)"/>
    <path class="cls-20" d="M944.67,1182.92c8.13,1.51,30.93,6.13,49.3,26.62,21.86,24.39,21.68,53.7,21.47,60.54-3.76,2.07-7.52,4.14-11.28,6.21-1.37-7.48-3.97-17.73-9.21-29.07-3.45-7.45-9.44-20.4-21.47-31.73-21.47-20.22-48.35-22.26-57.09-22.8-23-1.42-41.04,5.57-50.38,10.06,6.33-4.72,38.11-27.38,78.68-19.83Z"/>
    <polygon class="cls-10" points="789.56 1183.23 839.89 1172.28 795.08 1186.16 789.56 1183.23"/>
    <ellipse class="cls-12" cx="955.71" cy="1234.84" rx="5.03" ry="4.98" transform="translate(-583 1500.52) rotate(-62.06)"/>
    <path class="cls-10" d="M1026.48,1326.3c.45-.21,3.44,7.59,10.39,15.98,4.99,6.03,8.79,8.45,8.64,8.62-.24.29-10.67-5.82-16.43-17.11-1.96-3.85-2.9-7.36-2.6-7.5Z"/>
    <path class="cls-10" d="M1021.47,1333.25c.41-.29,4.71,6.87,13.03,13.92,5.97,5.07,10.14,6.79,10.03,6.99-.19.32-11.54-3.88-19.18-13.99-2.61-3.45-4.14-6.74-3.87-6.92Z"/>
    <path class="cls-10" d="M797.34,1189.28c15.05-4.56,30.11-9.13,45.16-13.69-13.79,4.69-27.59,9.39-41.38,14.08-1.26-.13-2.52-.26-3.78-.39Z"/>
  </g>
  <path class="cls-20" d="M3082.69,1061.89c-71.2,65.91-115.68,71.76-143.68,63.47-22.86-6.77-31.46-22.01-91.24-100.96-76.81-101.44-90.84-113.57-108.42-122.21-26.13-12.85-74.17-24.92-156.37,7.88,5.69-7.94,60.43-81.68,152.83-81.44,65.34.17,121.92,37.26,151.33,81.34,42.02,62.98,12.72,116.5,51.87,149.91,21.28,18.15,61.42,29.22,143.68,2.01Z"/>
  <path class="cls-18" d="M2866.9,1096.72c.1-5.08-.08-13.11-1.9-22.64-9.67-50.73-54.35-70.01-70.48-97.88-16.44-28.41-24.48-78.18,18.75-171.39,21.04,12.01,89.02,54.34,110.28,134.46,5.46,20.56,14.95,56.35-1.72,95.85-15,35.54-42.9,54.44-54.93,61.6Z"/>
  <path class="cls-10" d="M3018.11,952c-85.19,78.37-134.73,84.65-164.55,74.73-26.43-8.8-44.58-39.52-86.4-42.78-21.13-1.65-38.86,4.05-49.83,8.63.4-13.21,3.2-37.81,20.04-59.47,28.24-36.3,75.81-38.39,98.95-39.4,39.02-1.71,68.53,9.97,113.66,26.88,29.2,10.94,52.36,22.68,68.12,31.42Z"/>
  <path class="cls-12" d="M2916.17,1012.2c-8.29,1.47-51.49,8.24-92.29-20.32-40.7-28.48-49.19-71.22-50.67-79.54,4.47-1.77,52.03-19.67,96.29,9.48,47.19,31.09,46.77,86.81,46.68,90.37Z"/>
  <ellipse class="cls-7" cx="2852.86" cy="958.95" rx="23.1" ry="22.87" transform="translate(435.67 2752.15) rotate(-55.15)"/>
  <path class="cls-20" d="M2862.89,900.95c8.79,2.76,33.39,10.93,50.96,36.05,20.91,29.9,16.79,62.31,15.63,69.85-4.44,1.78-8.87,3.57-13.31,5.35-.52-8.46-2.01-20.14-6.29-33.39-2.82-8.71-7.71-23.84-19.49-37.98-21.03-25.24-50.5-31.1-60.1-32.87-25.25-4.65-46.14.67-57.08,4.37,7.64-4.37,45.83-25.17,89.68-11.39Z"/>
  <polygon class="cls-10" points="2634.05 872.53 2687.81 852.42 2640.6 874.88 2634.05 872.53"/>
  <ellipse class="cls-12" cx="2868.14" cy="959.86" rx="5.61" ry="5.55" transform="translate(441.47 2765.08) rotate(-55.15)"/>
  <path class="cls-10" d="M3024.85,1080.01c0-.56,9.28.04,21.01-3.11,8.42-2.26,12.64-4.99,12.75-4.76.18.38-10.84,8.11-24.95,8.7-4.81.2-8.8-.48-8.8-.84Z"/>
  <path class="cls-10" d="M3032.43,1090.22c-.13-.54,9.03-2.15,19.69-7.98,7.66-4.19,11.12-7.83,11.28-7.63.26.33-8.65,10.45-22.22,14.35-4.63,1.33-8.66,1.61-8.75,1.26Z"/>
  <path class="cls-10" d="M2643.55,883.57c15.26-8.63,30.51-17.25,45.77-25.88-13.86,8.46-27.71,16.92-41.57,25.38-1.4.17-2.8.33-4.2.5Z"/>
  <g>
    <path class="cls-20" d="M403.29,1154.59c61.65,55.82,104.82,62.08,133.15,57.05,35.67-6.34,66.98-34.01,135.52-39.95,11.67-1.01,21.27-1.12,27.37-1.06-68.73-68.8-112.28-76.81-140.2-69-38.65,10.81-60.92,55.73-116.01,58.46-16.84.83-30.81-2.57-39.82-5.49Z"/>
    <path class="cls-18" d="M478.25,1249.55c2.53-3.79,6.79-9.61,13.01-15.7,33.14-32.4,75.93-23.69,102.11-35.95,26.68-12.49,58.12-45.03,74.09-135.85-21.65-1.94-93.44-5.63-150.18,42.47-14.56,12.35-39.91,33.84-47.89,71.48-7.18,33.87,3.68,62.1,8.86,73.54Z"/>
    <path class="cls-10" d="M441.1,1065.41c22.55,101.41,55.82,131.45,82.87,139.43,23.98,7.08,53.1-6.24,85.58,12.8,16.41,9.62,26.55,22.91,32.28,31.91,6.48-9.93,17.04-29.49,15.74-54.08-2.19-41.22-36.15-67.15-52.67-79.77-27.86-21.27-55.59-27.8-97.5-38.49-27.12-6.92-50.2-10.15-66.29-11.8Z"/>
    <path class="cls-12" d="M485.31,1162.03c5.35,5.33,33.69,32.47,78.4,32.37,44.58-.1,72.76-27.23,78.11-32.6-2.38-3.6-28.23-41.17-75.78-42.4-50.7-1.3-78.97,39.95-80.73,42.62Z"/>
    <ellipse class="cls-5" cx="559.25" cy="1155.28" rx="20.52" ry="20.73"/>
    <path class="cls-20" d="M581.61,1107.42c-7.89-2.47-30.2-9.08-56.03.42-30.73,11.3-44.32,37.28-47.33,43.43,2.35,3.59,4.71,7.18,7.06,10.77,4.72-5.96,11.81-13.8,21.76-21.36,6.54-4.97,17.9-13.6,33.83-17.97,28.44-7.8,53.14,2.99,61.12,6.62,20.98,9.53,33.64,24.15,39.8,32.49-3.38-7.13-20.84-42.05-60.21-54.39Z"/>
    <polygon class="cls-10" points="691.65 1167.8 652.32 1134.55 685.41 1167.8 691.65 1167.8"/>
    <ellipse class="cls-12" cx="547.53" cy="1148.12" rx="4.98" ry="5.03"/>
    <path class="cls-10" d="M441.53,1162.03c-.05-.5-8.29.84-19.03-.96-7.72-1.29-11.72-3.37-11.8-3.15-.12.36,10.39,6.31,23.04,5.62,4.32-.23,7.82-1.18,7.79-1.51Z"/>
    <path class="cls-10" d="M440.54,1167.8c.03-.5-8.32-.47-18.66-3.92-7.43-2.48-11.07-5.15-11.18-4.95-.18.33,9.3,7.85,21.9,9.15,4.3.44,7.91.05,7.93-.28Z"/>
    <path class="cls-10" d="M684.57,1169.23c-11.16-11.09-22.32-22.17-33.48-33.26,9.99,10.61,19.97,21.22,29.96,31.83,1.17.48,2.35.95,3.52,1.43Z"/>
  </g>
  <path class="cls-9" d="M483.73,1224.38c2.92-2.92,5.84-5.84,8.76-8.76.93-.93.99-2.62,0-3.54s-2.55-.99-3.54,0c-2.92,2.92-5.84,5.84-8.76,8.76-.93.93-.99,2.62,0,3.54s2.55.99,3.54,0h0Z"/>
  <path class="cls-9" d="M468.27,1246.13c7.29-9.1,14.58-18.2,21.87-27.3.84-1.05,1.03-2.51,0-3.54-.94-.94-2.61-.99-3.54,0-12.14,12.86-23.76,26.22-34.8,40.04l3.54,3.54c11.55-14.53,23.82-28.48,36.77-41.78,3.7-3.8,7.46-7.54,11.26-11.23l-3.93-3.03c-9.32,19.11-18.63,38.22-27.95,57.33-1.44,2.96,2.38,5.03,4.32,2.52,12.95-16.82,25.9-33.65,38.86-50.47,3.64-4.73,7.29-9.46,10.93-14.2,1.72-2.24-2.07-5.16-3.93-3.03-18.85,21.64-38.36,42.69-58.53,63.11-5.75,5.82-11.54,11.58-17.39,17.3l3.93,3.03c9.84-13.25,19.8-26.41,29.87-39.49,9.99-12.97,20.1-25.85,30.33-38.63,5.84-7.3,11.71-14.58,17.62-21.82.85-1.05,1.02-2.51,0-3.54-.88-.88-2.68-1.05-3.54,0-10.48,12.85-20.86,25.79-31.11,38.83s-20.55,26.37-30.65,39.69c-5.65,7.45-11.26,14.93-16.83,22.43-1.73,2.33,1.96,4.95,3.93,3.03,20.52-20.06,40.42-40.75,59.65-62.05,5.48-6.07,10.9-12.19,16.27-18.35l-3.93-3.03c-12.95,16.82-25.9,33.65-38.86,50.47-3.64,4.73-7.29,9.46-10.93,14.2l4.32,2.52c9.32-19.11,18.63-38.22,27.95-57.33,1.21-2.48-1.8-5.09-3.93-3.03-13.32,12.92-26,26.5-37.96,40.7-3.42,4.06-6.77,8.16-10.07,12.31-.84,1.06-1.03,2.5,0,3.54.87.87,2.69,1.06,3.54,0,11.04-13.82,22.65-27.18,34.8-40.04l-3.54-3.54c-7.29,9.1-14.58,18.2-21.87,27.3-.84,1.05-1.03,2.51,0,3.54.87.87,2.69,1.06,3.54,0h0Z"/>
  <path class="cls-9" d="M410.43,1149.33c20.56-.11,41.12-.22,61.69-.32s41.12-.22,61.69-.32c20.48-.11,40.96-.21,61.44-.32s41.12-.22,61.69-.32,41.12-.22,61.69-.32c2.5-.01,4.99-.03,7.49-.04,2.86-.01,3.34-4.07.66-4.91-8.96-2.83-18.47-2.78-27.71-1.59s-19.07,3.37-28.57,5.19c-19.5,3.75-39.04,7.26-58.63,10.54s-39.21,6.32-58.86,9.13-39.23,5.36-58.89,7.69c-19.72,2.33-39.46,4.43-59.23,6.29-2.45.23-4.9.46-7.35.68-3.18.29-3.25,4.82,0,5,23.75,1.33,47.49,2.66,71.24,3.99,23.66,1.33,47.33,2.65,70.99,3.98,13.37.75,26.73,1.5,40.1,2.25v-5c-26.95-.53-53.69-4.43-79.95-10.38-26.41-5.98-52.33-13.96-77.98-22.63-14.45-4.88-28.82-9.99-43.18-15.1-2.26-.81-4.4,2.64-2.43,4.18,13.92,10.85,27.83,21.7,41.75,32.54,3.94,3.07,7.88,6.14,11.82,9.21l3.03-3.93c-15.74-8.68-31.28-17.74-46.58-27.17-4.32-2.66-8.63-5.36-12.92-8.08-2.73-1.73-5.22,2.58-2.52,4.32,14.78,9.5,29.55,18.99,44.33,28.49,4.18,2.69,8.36,5.38,12.55,8.06,2,1.28,4.96-1.39,3.42-3.42-11.17-14.69-23.82-28.19-37.84-40.2-2.43-2.08-5.72,1.13-3.54,3.54,13.04,14.4,26.58,28.33,40.62,41.75,4.03,3.85,8.09,7.66,12.2,11.43,2.39,2.19,5.64-1.12,3.54-3.54-8.7-10-16.52-20.72-23.41-32.04l-3.93,3.03c12.89,11.1,25.78,22.21,38.67,33.31,3.71,3.2,7.42,6.39,11.13,9.59,1.02.88,2.53,1,3.54,0,.9-.9,1.03-2.65,0-3.54-12.89-11.1-25.78-22.21-38.67-33.31-3.71-3.2-7.42-6.39-11.13-9.59-2.06-1.77-5.48.47-3.93,3.03,7.11,11.68,15.21,22.74,24.19,33.06l3.54-3.54c-14.31-13.13-28.14-26.77-41.47-40.9-3.82-4.05-7.6-8.15-11.34-12.28l-3.54,3.54c13.67,11.72,26.15,24.85,37.06,39.19l3.42-3.42c-14.78-9.5-29.55-18.99-44.33-28.49-4.18-2.69-8.36-5.38-12.55-8.06l-2.52,4.32c15.18,9.64,30.58,18.92,46.2,27.81,4.41,2.51,8.85,4.99,13.29,7.45,1.08.6,2.47.36,3.23-.64.67-.9.8-2.51-.2-3.28-13.92-10.85-27.83-21.7-41.75-32.54-3.94-3.07-7.88-6.14-11.82-9.21l-2.43,4.18c25.76,9.17,51.55,18.32,77.75,26.19,25.9,7.78,52.24,14.32,79.02,18.21,15.13,2.2,30.38,3.58,45.67,3.88,3.24.06,3.19-4.82,0-5-23.75-1.33-47.49-2.66-71.24-3.99s-47.33-2.65-70.99-3.98c-13.37-.75-26.73-1.5-40.1-2.25v5c19.69-1.8,39.36-3.82,59.01-6.09,19.7-2.27,39.38-4.78,59.02-7.51,19.67-2.74,39.3-5.71,58.89-8.92,19.51-3.2,38.99-6.62,58.42-10.28,9.6-1.81,19.17-3.81,28.79-5.54,8.87-1.59,18.14-2.82,27.11-1.36,2.26.37,4.49.9,6.67,1.59l.66-4.91c-20.56.11-41.12.22-61.69.32s-41.12.22-61.69.32c-20.48.11-40.96.21-61.44.32s-41.12.22-61.69.32-41.12.22-61.69.32c-2.5.01-4.99.03-7.49.04-3.22.02-3.22,5.02,0,5h0Z"/>
  <path class="cls-9" d="M463.46,1150.65c27.75-.27,55.51-.25,83.26.08,7.81.09,15.62.21,23.43.35v-5c-16.64.78-33.28,1.56-49.92,2.35-16.72.79-33.45,1.57-50.17,2.36-9.4.44-18.8.88-28.21,1.33-3.2.15-3.23,5.11,0,5,22.06-.75,44.14-1.1,66.22-1.02,22.08.08,44.15.57,66.21,1.49,12.44.51,24.87,1.16,37.3,1.94v-5c-22.22-.13-44.44-.58-66.65-1.35-22.13-.77-44.24-1.86-66.33-3.27-12.5-.8-24.98-1.7-37.46-2.7v5c21.82-.37,43.64.29,65.42,1.65,21.8,1.36,43.55,3.4,65.25,5.77,12.17,1.33,24.33,2.75,36.49,4.22v-5c-27.88-.52-55.75-1.35-83.62-2.5-27.86-1.15-55.71-2.62-83.54-4.4-15.57-1-31.13-2.1-46.69-3.29v5c19.39-.45,38.78-.44,58.17.23s38.72,1.98,57.97,4.14c10.83,1.21,21.62,2.71,32.37,4.5l.66-4.91c-24.22,1.16-48.46,2.11-72.7,2.85s-48.49,1.26-72.74,1.57c-13.62.17-27.24.28-40.86.32v5c32.29-1.22,64.62-1.16,96.9.18,9.11.38,18.21.86,27.3,1.44,1.35.09,2.5-1.21,2.5-2.5,0-1.49-1.15-2.33-2.5-2.5-16.89-2.12-33.77-4.25-50.63-6.62-16.53-2.32-33.15-4.62-49.52-7.92-8.76-1.77-17.5-4.04-25.67-7.72l-1.26,4.66c26.04-.87,52.13.08,78.03,2.89,7.27.79,14.52,1.72,21.76,2.81l.66-4.91c-18.88-.97-37.66-3.36-56.19-7.17l-1.33,4.82c7.59,1.06,14.91,3.44,21.63,7.13l.6-4.57c-5.6.94-11.21,1.78-16.84,2.5-1.23.16-2.3.83-2.46,2.17-.14,1.14.55,2.52,1.79,2.74,11.65,2.09,23.3,4.18,34.96,6.27l.66-4.91c-17.11-.45-34.23-.9-51.34-1.35v5c13.82-.28,27.65.14,41.43,1.24v-5c-7.44.65-14.76,2.19-21.8,4.68-2.68.95-2.19,4.71.66,4.91,8.33.6,16.68.98,25.04,1.13,2.74.05,3.5-4.44.66-4.91-12.8-2.13-25.6-4.26-38.41-6.39-1.33-.22-2.69.35-3.08,1.75-.34,1.23.42,2.81,1.75,3.08,18.86,3.75,37.64,7.89,56.33,12.45,5.33,1.3,10.64,2.63,15.95,4l.66-4.91c-18.98.26-37.96-.46-56.86-2.14v5c21.43,1.15,42.77,3.8,63.82,7.97,5.97,1.18,11.92,2.49,17.85,3.91l.66-4.91c-30.66-1.51-61.28-3.93-91.8-7.27-8.7-.95-17.38-1.98-26.06-3.08v5c16.72-.36,33.44-.83,50.15-1.42,16.71-.59,33.42-1.29,50.13-2.11,9.47-.46,18.95-.97,28.41-1.5,3.19-.18,3.24-5.09,0-5-28.75.84-57.51,1.68-86.27,1.06-8.14-.17-16.28-.47-24.41-.91v5c29.39-2.74,58.92-3.8,88.43-3.13,8.24.19,16.48.51,24.71.96v-5c-7.6-.2-15.21-.39-22.81-.59-3.22-.08-3.22,4.92,0,5,7.6.2,15.21.39,22.81.59,3.23.08,3.2-4.83,0-5-29.47-1.61-59.03-1.53-88.48.27-8.23.5-16.45,1.14-24.66,1.91-3.17.3-3.26,4.82,0,5,28.72,1.55,57.51,1.25,86.25.53,8.15-.2,16.29-.44,24.43-.68v-5c-16.7.95-33.4,1.78-50.11,2.5s-33.42,1.32-50.14,1.81c-9.48.28-18.96.52-28.45.72-1.35.03-2.5,1.13-2.5,2.5s1.15,2.33,2.5,2.5c30.46,3.85,61.03,6.81,91.66,8.83,8.73.58,17.46,1.08,26.2,1.51,2.81.14,3.4-4.25.66-4.91-21.22-5.09-42.81-8.67-64.54-10.71-6.14-.58-12.3-1.03-18.46-1.36-1.35-.07-2.5,1.2-2.5,2.5,0,1.45,1.15,2.38,2.5,2.5,18.9,1.68,37.88,2.39,56.86,2.14,2.78-.04,3.45-4.2.66-4.91-18.63-4.79-37.35-9.18-56.17-13.15-5.36-1.13-10.74-2.23-16.11-3.3l-1.33,4.82c12.8,2.13,25.6,4.26,38.41,6.39l.66-4.91c-8.35-.16-16.7-.53-25.04-1.13l.66,4.91c6.61-2.35,13.48-3.9,20.47-4.51,3.2-.28,3.23-4.74,0-5-13.78-1.1-27.61-1.52-41.43-1.24-3.22.06-3.22,4.92,0,5,17.11.45,34.23.9,51.34,1.35,2.75.07,3.48-4.4.66-4.91-11.65-2.09-23.3-4.18-34.96-6.27l-.66,4.91c6.07-.78,12.13-1.67,18.17-2.68,2.16-.36,2.37-3.59.6-4.57-7.08-3.89-14.81-6.52-22.82-7.64-1.34-.19-2.68.33-3.08,1.75-.34,1.24.42,2.8,1.75,3.08,18.95,3.89,38.19,6.35,57.51,7.34,2.76.14,3.48-4.49.66-4.91-26.15-3.92-52.56-6.02-79.01-6.18-7.37-.05-14.75.06-22.12.31-2.28.08-3.66,3.57-1.26,4.66,15.07,6.8,31.55,9.26,47.74,11.86,17.17,2.75,34.39,5.14,51.62,7.4,9.65,1.26,19.31,2.48,28.97,3.69v-5c-32.24-2.05-64.57-2.84-96.87-2.34-9.11.14-18.22.39-27.33.73-3.2.12-3.23,5.01,0,5,24.25-.07,48.5-.35,72.75-.85,24.25-.5,48.49-1.2,72.72-2.13,13.61-.52,27.22-1.1,40.82-1.76,1.24-.06,2.31-.9,2.46-2.17.13-1.12-.55-2.54-1.79-2.74-19.33-3.22-38.82-5.5-58.36-6.99-19.41-1.48-38.88-2.2-58.35-2.34-11.05-.08-22.09.03-33.13.28-3.25.07-3.18,4.76,0,5,27.8,2.14,55.63,3.96,83.47,5.46,27.84,1.51,55.71,2.69,83.58,3.57,15.59.49,31.19.88,46.79,1.17,1.35.03,2.5-1.17,2.5-2.5,0-1.49-1.15-2.34-2.5-2.5-21.69-2.61-43.39-5.1-65.14-7.12-21.73-2.01-43.52-3.54-65.33-4.23-12.23-.38-24.46-.49-36.69-.29-3.26.06-3.17,4.75,0,5,22.15,1.77,44.33,3.23,66.52,4.36,22.11,1.13,44.24,1.94,66.37,2.43,12.52.28,25.04.45,37.56.52,3.24.02,3.19-4.8,0-5-22.03-1.38-44.09-2.35-66.16-2.9-22.07-.55-44.15-.68-66.22-.39-12.45.16-24.89.46-37.34.88v5c16.64-.78,33.28-1.56,49.92-2.35s33.45-1.57,50.17-2.36c9.4-.44,18.8-.88,28.21-1.33,3.2-.15,3.24-4.94,0-5-27.75-.5-55.51-.7-83.26-.59-7.81.03-15.62.08-23.43.16-3.22.03-3.22,5.03,0,5h0Z"/>
  <path class="cls-9" d="M413.76,1193.64c2.35,15.73,15.77,30.77,29.67,37.79s28.29,8.76,43.32,8.74c22.35-.03,44.49-4.67,66.56-7.71-3.77.51-7.53,1.01-11.3,1.52,13.17-1.76,26.4-2.97,39.68-3.55,12.68-.55,25.87-1.21,38.52.12-3.77-.51-7.53-1.01-11.3-1.52,1.32.23,2.41.52,3.65,1.03-3.38-1.43-6.77-2.86-10.15-4.28l1.02.63-8.6-6.65.86.81c-2.22-2.87-4.43-5.73-6.65-8.6l.66,1.01-4.28-10.15.44,1.39-1.52-11.3c.14.99.14,1.97-.03,2.96l1.52-11.3c-1.6,9.68-3.51,19.32-5.28,28.98-2,10.94-2.04,22.94,4.28,32.75,5.09,7.89,11.63,13.72,20.32,17.39,18.38,7.76,43.51,1.58,53.07-17.39.62-1.23,1.27-2.33,2.05-3.47-2.22,2.87-4.43,5.73-6.65,8.6,1.65-2.14,3.51-3.93,5.63-5.61l-8.6,6.65c5.28-3.88,11.24-6.59,16.8-10.02,8.99-5.54,16.41-12.95,24.55-19.54-2.87,2.22-5.73,4.43-8.6,6.65.86-.68,1.74-1.32,2.64-1.93,7.77-5.45,13.63-11.41,17.39-20.32,3.56-8.44,5.04-18.7,2.14-27.67-2.27-7.01-5.95-13.33-10.93-18.75-7.72-8.41-19.15-11.6-30.05-12.45-1.17-.07-2.34-.18-3.5-.33,3.77.51,7.53,1.01,11.3,1.52-5.27-.74-10.3-2.11-15.22-4.14l10.15,4.28-1.6-.72c-7.15,26.4-14.3,52.8-21.45,79.2,6.43-.21,12.81.08,19.2.87-3.77-.51-7.53-1.01-11.3-1.52,1.27.17,2.54.36,3.8.58,3.77-27.83,7.53-55.65,11.3-83.48-19.29.18-38.58.77-57.84,1.81-21.61,1.17-43.63,2.66-64.6,8.3-10.28,2.77-20.03,10.36-25.4,19.53s-7.59,22.54-4.28,32.75c7.13,22.02,29.27,35.87,52.28,29.68,6.79-1.82,13.65-3.13,20.61-4.11-3.77.51-7.53,1.01-11.3,1.52,29.86-3.9,60.46-4.21,90.53-4.49,21.22-.2,38.94-16.02,41.74-36.85,1.22-9.06.06-19.18-5.04-27.1-5.62-8.71-14.71-17.82-25.4-19.53-11.39-1.83-22.76-3.35-34.3-2.97-18.03.6-36.77,12.71-40.98,31.2-1.7,7.47-1.7,15.12,0,22.6,2.07,9.08,10.8,21.48,19.53,25.4,14.45,6.49,25.83,9.75,41.77,10.99-7.15-26.4-14.3-52.8-21.45-79.2-7.98,5.6-14.87,12.14-22.41,18.19,2.87-2.22,5.73-4.43,8.6-6.65-5.52,4.14-11.78,6.97-17.62,10.6-10.69,6.64-21.12,15.57-26.89,27.02,25.89,10.92,51.79,21.83,77.68,32.75,3.98-21.75,12.56-46.82-.08-67.09-9.87-15.84-26.28-22.56-44.16-23.69-6.99-.44-14.05-.41-21.04-.35-31.57.25-62.43,5.14-93.63,9.42,3.77-.51,7.53-1.01,11.3-1.52-13.85,1.84-28.02,3.23-41.95,1.46,3.77.51,7.53,1.01,11.3,1.52-2.81-.46-5.43-1.12-8.08-2.16l10.15,4.28c-1.39-.66-2.59-1.33-3.85-2.19,2.87,2.22,5.73,4.43,8.6,6.65-1.48-1.18-2.72-2.41-3.88-3.92,2.22,2.87,4.43,5.73,6.65,8.6-.53-.74-.98-1.53-1.35-2.36l4.28,10.15c-.52-1.34-.83-2.49-1.05-3.92-1.47-9.81-11.38-20.63-19.53-25.4-9.11-5.33-22.54-7.59-32.75-4.28s-20.21,9.7-25.4,19.53l-4.28,10.15c-2.02,7.53-2.02,15.06,0,22.6h0Z"/>
  <path class="cls-9" d="M470.13,1242.56c8.46-.46,16.97-.16,25.36,1.02l-.6-4.57c-4.82,2.34-9.9,4.04-15.13,5.16l3.16,2.41c.22-2.09.01-4.31-1.69-5.75s-4.03-1.44-5.73.03l3.93,3.03c2.61-6.02,6.37-11.38,11.19-15.85l-3.93-3.03c-5.95,10.98-11.9,21.96-17.85,32.94-1.36,2.5,1.83,4.99,3.93,3.03,11.29-10.57,23.28-20.38,35.92-29.3,3.64-2.57,7.33-5.07,11.07-7.49,2.69-1.75.19-6.08-2.52-4.32-13.26,8.61-25.94,18.13-37.9,28.47-3.43,2.97-6.8,6-10.11,9.1l3.93,3.03c5.95-10.98,11.9-21.96,17.85-32.94,1.36-2.51-1.83-4.98-3.93-3.03-5.09,4.73-9.2,10.49-11.97,16.86-1.11,2.57,1.68,4.97,3.93,3.03l-.72.31c-.08.04-.16.02-.22-.06-.59.37.24-.67-.1.07-.18.39-.02,1.4-.07,1.87-.18,1.72,1.73,2.72,3.16,2.41,5.66-1.21,11.12-3.14,16.32-5.66,1.82-.88,1.53-4.27-.6-4.57-8.85-1.24-17.76-1.68-26.69-1.19-3.2.17-3.22,5.18,0,5h0Z"/>
  <path class="cls-8" d="M389.97,1112.49c-57.8,15.33-105.13,51.13-101.25,60.14,4.1,9.52,59.41-24.82,131.15-7.18,33.72,8.29,30.77,18.16,63.52,24.4,42.18,8.04,47.92-8.15,120.9-8.61,0,0,47.85-.3,86.06,15.79h0c80.07-20.94,103.97-33.34,102.99-40.96-1.12-8.68-34.26-8.53-37.2-22.78-2.97-14.39,28.16-27.69,25.81-35.68-2.16-7.35-31.98-8.16-122.34,7.5,64.56,0,81.31,3.68,81.7,7.67.49,5.08-25.65,9.28-25.72,18.02-.09,11.89,48.22,15.92,48.42,26.49.17,9.24-36.46,21.57-75.84,21.92-45.06.4-47.52-15.3-88.32-16.62-56.4-1.82-73.38,27.49-112.59,13.39-23.27-8.37-20.28-19.76-44.61-30.15-51.97-22.19-110.08,10.82-111.46,7.31-.82-2.08,19.74-13.24,98.05-47.64-10.29.97-23.82,2.89-39.27,6.99Z"/>
  <g>
    <path class="cls-20" d="M1459.82,1160.02c61.65,55.82,104.82,62.08,133.15,57.05,35.67-6.34,66.98-34.01,135.52-39.95,11.67-1.01,21.27-1.12,27.37-1.06-68.73-68.8-112.28-76.81-140.2-69-38.65,10.81-60.92,55.73-116.01,58.46-16.84.83-30.81-2.57-39.82-5.49Z"/>
    <path class="cls-18" d="M1534.78,1254.98c2.53-3.79,6.79-9.61,13.01-15.7,33.14-32.4,75.93-23.69,102.11-35.95,26.68-12.49,58.12-45.03,74.09-135.85-21.65-1.94-93.44-5.63-150.18,42.47-14.56,12.35-39.91,33.84-47.89,71.48-7.18,33.87,3.68,62.1,8.86,73.54Z"/>
    <path class="cls-10" d="M1497.63,1070.84c22.55,101.41,55.82,131.45,82.87,139.43,23.98,7.08,53.1-6.24,85.58,12.8,16.41,9.62,26.55,22.91,32.28,31.91,6.48-9.93,17.04-29.49,15.74-54.08-2.19-41.22-36.15-67.15-52.67-79.77-27.86-21.27-55.59-27.8-97.5-38.49-27.12-6.92-50.2-10.15-66.29-11.8Z"/>
    <path class="cls-12" d="M1541.84,1167.46c5.35,5.33,33.69,32.47,78.4,32.37,44.58-.1,72.76-27.23,78.11-32.6-2.38-3.6-28.23-41.17-75.78-42.4-50.7-1.3-78.97,39.95-80.73,42.62Z"/>
    <ellipse class="cls-7" cx="1615.78" cy="1160.71" rx="20.52" ry="20.73"/>
    <path class="cls-20" d="M1638.14,1112.85c-7.89-2.47-30.2-9.08-56.03.42-30.73,11.3-44.32,37.28-47.33,43.43,2.35,3.59,4.71,7.18,7.06,10.77,4.72-5.96,11.81-13.8,21.76-21.36,6.54-4.97,17.9-13.6,33.83-17.97,28.44-7.8,53.14,2.99,61.12,6.62,20.98,9.53,33.64,24.15,39.8,32.49-3.38-7.13-20.84-42.05-60.21-54.39Z"/>
    <polygon class="cls-10" points="1748.18 1173.23 1708.85 1139.97 1741.94 1173.23 1748.18 1173.23"/>
    <ellipse class="cls-12" cx="1604.06" cy="1153.54" rx="4.98" ry="5.03"/>
    <path class="cls-10" d="M1498.06,1167.46c-.05-.5-8.29.84-19.03-.96-7.72-1.29-11.72-3.37-11.8-3.15-.12.36,10.39,6.31,23.04,5.62,4.32-.23,7.82-1.18,7.79-1.51Z"/>
    <path class="cls-10" d="M1497.07,1173.23c.03-.5-8.32-.47-18.66-3.92-7.43-2.48-11.07-5.15-11.18-4.95-.18.33,9.3,7.85,21.9,9.15,4.3.44,7.91.05,7.93-.28Z"/>
    <path class="cls-10" d="M1741.1,1174.66c-11.16-11.09-22.32-22.17-33.48-33.26,9.99,10.61,19.97,21.22,29.96,31.83,1.17.48,2.35.95,3.52,1.43Z"/>
  </g>
  <path class="cls-9" d="M1463.34,1168.43c9.27,3.92,18.55,7.84,27.82,11.76,1.24.53,2.68.37,3.42-.9.61-1.05.35-2.89-.9-3.42-9.27-3.92-18.55-7.84-27.82-11.76-1.24-.53-2.68-.37-3.42.9-.61,1.05-.35,2.89.9,3.42h0Z"/>
  <path class="cls-9" d="M1453.03,1188.64c13.89,7.7,27.54,15.8,40.92,24.35,6.56,4.19,13.05,8.48,19.47,12.87,3.08,2.11,6.15,4.24,9.2,6.39,1.52,1.08,3.02,2.2,4.57,3.25,2.7,1.98,2.04,1.42-1.97-1.67,2.18,2.15,1.49,1.17-2.08-2.93l-3.02-7.17.4,1.16-1.07-7.98.04,1.17,1.07-7.98-.41,1.15c-5.26,14.81,5.95,33.49,20.95,36.9,16.82,3.83,31.27-5.1,36.9-20.95,4.19-11.8-.34-24.91-8.44-33.8-3.9-4.29-8.9-7.47-13.61-10.82-7.86-5.6-15.82-11.06-23.89-16.35-15.87-10.42-32.15-20.22-48.76-29.43-13.72-7.61-33.36-3.78-41.05,10.76s-3.9,32.92,10.76,41.05h0Z"/>
  <path class="cls-9" d="M1524.4,1273.54c20.63-.84,41.26-1.69,61.88-2.53,12.74-.52,25.94-8.9,28.93-22.02s-2.23-26.31-13.79-33.88c-27.46-17.97-54.92-35.94-82.37-53.92-7.71,18.28-15.41,36.55-23.12,54.83,40.4,14.57,83.69,20.5,126.46,22.05,20.49.75,41.01.43,61.49-.43,20.12-.84,40.49-1.53,60.51-3.73,12.26-1.35,24.26-3.62,35.05-10.02,14.73-8.74,22.83-22.82,24.05-39.73.66-9.27-4.81-18.14-11.82-23.56-7.83-6.05-16.54-7.39-26.15-5.37-24.26,5.1-48.69,9.31-73.26,12.62l7.98-1.07c-25.87,3.43-51.85,5.88-77.91,7.28-23.78,1.27-47.78,2.21-71.47-.78l7.98,1.07c-13.03-1.76-25.76-4.81-37.9-9.9l7.17,3.02c-3.15-1.37-6.22-2.86-9.25-4.47-7.71,18.28-15.41,36.55-23.12,54.83,42.73,7.32,85.67,7.56,128.87,6.13,41-1.36,82.27-3.69,123.19.47l-7.98-1.07c1.7.23,3.23.53,4.88,1.02l-7.17-3.02c3.82,1.66,2.56,2.71-1.75-1.97-1.12-1.22-4.38-6.92-2.28-2.35l-3.02-7.17c.4,1.12.68,2.16.9,3.32l-1.07-7.98c.25,2.07.22,4.03-.02,6.1l1.07-7.98c-.43,2.57-1.14,4.97-2.14,7.38l3.02-7.17c-.72,1.54-1.57,2.94-2.44,4.4-2.85,4.75,3.49-3.84,1.26-1.63-.75.74-1.42,1.6-2.15,2.37-1.48,1.54-3.04,3.01-4.65,4.41s-5.23,3.65,1.69-1.15c-.89.61-1.73,1.29-2.61,1.91-7.93,5.64-16.47,10.48-24.94,15.25-18.11,10.2-36.8,19.32-55.94,27.41l7.17-3.02c-19.17,8.03-38.76,14.99-58.7,20.84,7.71,18.28,15.41,36.55,23.12,54.83,20.82-9.87,41.63-19.73,62.45-29.6,6.46-3.06,11.96-11.28,13.79-17.93,1.98-7.18,1.08-16.74-3.02-23.12-9.43-14.62-25.83-17.97-41.05-10.76-20.82,9.87-41.63,19.73-62.45,29.6-6.18,2.93-12.3,11.38-13.79,17.93s-1.18,13.16,1.51,19.53c5.67,13.44,20.93,21.61,35.39,17.37,31.49-9.24,62.22-21.28,91.76-35.57,14.03-6.79,27.76-14.17,41.15-22.14,15.54-9.25,29.56-19.69,40.41-34.31s14.09-38.27,4.38-54.92c-7.15-12.26-19.19-18.46-32.78-20.29-8.88-1.19-17.84-1.75-26.79-2.18-32.49-1.54-65.02-.02-97.49,1.08-36.85,1.25-73.84,1.91-110.48-2.91l7.98,1.07c-4.45-.61-8.88-1.29-13.31-2.05-7.79-1.34-16.12-1.49-23.12,3.02-5.57,3.59-9.69,8.21-12.27,14.35-5.57,13.19-.98,30.42,12.27,37.46,33.87,18,72.18,21.25,109.91,20.22,40-1.09,79.84-4.98,119.43-10.67,21.31-3.06,42.5-6.84,63.57-11.28l-37.98-28.93-.09,1.19,1.07-7.98c-.22,1.52-.58,2.89-1.15,4.32l3.02-7.17c-1.56,3.22-2.51,1.96,2.4-2.41-.26.23-2.27,2.07-2.35,2.34.35-1.25,7.14-4.84,2.25-2.07-1.06.6-2.16,1.11-3.28,1.61l7.17-3.02c-4.78,1.97-9.75,3.13-14.86,3.85,2.66-.36,5.32-.71,7.98-1.07-13.73,1.72-27.75,2.09-41.55,2.88-14.39.82-28.79,1.45-43.2,1.72-28.92.54-57.89-.43-86.58-4.28l7.98,1.07c-23.99-3.33-47.61-8.75-70.42-16.98-6.7-2.42-17.21-.79-23.12,3.02-5.57,3.59-9.69,8.21-12.27,14.35-5.96,14.11-.36,29.19,12.27,37.46,27.46,17.97,54.92,35.94,82.37,53.92,5.05-18.63,10.09-37.27,15.14-55.9-20.63.84-41.26,1.69-61.88,2.53-15.66.64-30.75,13.34-30,30,.7,15.71,13.2,30.69,30,30h0Z"/>
  <path class="cls-9" d="M1516.05,1216.54c33.36,1.85,66.72,3.94,100.1,5.5,16.67.78,33.34,1.03,49.99-.16s32.29-3.32,48.4-5.23c18.66-2.21,37.38-4,56.2-3.61-5.05-18.63-10.09-37.27-15.14-55.9-5.38,3.45-10.78,6.8-16.63,9.41l7.17-3.02c-3.88,1.6-7.72,2.71-11.87,3.37l7.98-1.07c-8.18,1-16.48.64-24.71.89-7.77.24-15.54.67-23.3,1.28-18.51,1.44-36.95,3.84-55.3,6.65-32.46,4.97-64.57,11.92-96.65,18.88-15.36,3.33-25.75,22.08-20.95,36.9,5.23,16.17,20.45,24.52,36.9,20.95,36.84-7.99,73.72-15.92,111.1-20.95l-7.98,1.07c19.92-2.62,39.96-4.44,60.06-4.88,11.42-.25,23,.06,34.15-2.94,10.9-2.93,20.87-8.69,30.3-14.72,10.82-6.93,17.96-20.97,13.79-33.88s-15.27-21.74-28.93-22.02c-32.85-.68-65.2,5.44-97.81,8.29-33.1,2.89-66.22.23-99.3-1.6-19.19-1.06-38.39-2.13-57.58-3.19-15.7-.87-30.7,14.42-30,30,.76,17,13.19,29.07,30,30h0Z"/>
  <path class="cls-8" d="M1459.82,1124.81c-64.04.51-114.46,61.19-107.98,72.06,7.2,12.08,85.47-36,119.34-5.86,7.01,6.24,11.98,15.7,25.88,20.65,16.54,5.89,27.66-1.05,37.72,7.61,8.63,7.43,5.46,16.84,13.01,20.02,7.92,3.33,13.97-5.95,28.32-9.99,21.69-6.11,27.99,9.42,47.57,4.64,20.3-4.95,20.76-23.4,43.73-28.46,3.44-.76,8.62-1.49,28.7,1.86,14.11,2.35,33.84,6.43,57.36,13.93,0,0,0,0,0,0,9.27,3.9,22.52-15.61,35.26-25.4,27.51-21.14,66.2-7.43,67.72-15.56,1.24-6.63-25.29-11.22-27.79-26.77-2.34-14.55,18.62-24.61,16.41-31.69-2.27-7.25-28.28-9.55-122.34,7.5,64.56,0,81.31,3.68,81.7,7.67.5,5.22-26.21,10.31-25.72,18.02.59,9.18,39.23,13.31,38.81,17.11-.35,3.1-25.73-2.97-46.79,10.89-13.12,8.63-9.73,15.24-19.44,20.41-24.66,13.13-52.68-26.26-88.32-16.62-17.11,4.63-12.87,14.31-33.95,22.04-34.69,12.72-70.39-4.63-78.65-8.64-21.59-10.49-19.1-16.99-35.76-25.57-51.01-26.25-114.89,13.87-120.32,2.72-3.08-6.34,16.32-21.91,26.73-28.87,28.7-19.18,58.67-19.43,71.32-18.77-8.86-2.76-19.89-5.03-32.54-4.93Z"/>
  <circle class="cls-11" cx="2556.98" cy="217.69" r="27.05"/>
  <circle class="cls-21" cx="3042.8" cy="557.59" r="40.99"/>
  <circle class="cls-22" cx="2529.93" cy="125.81" r="13.53"/>
  <g>
    <ellipse class="cls-23" cx="2832.83" cy="299.8" rx="209.97" ry="201.46"/>
    <path class="cls-15" d="M2709,137.1s68.85,18.11,43.48,43.48c-25.37,25.37-52.27,111.29-81.78,81.78-29.51-29.51-35.52-30.7-35.52-30.7,5.23-13.78,14.21-32.91,29.67-52.75,15.55-19.95,32.01-33.37,44.15-41.81"/>
    <path class="cls-15" d="M2806.35,179.98c-4.56-.75-25.7,41.67-15,88.64,3.26,14.31,10.71,46.98,41.48,63.82,28.91,15.82,63.49,9.5,85.98-6.25,5.88-4.11,36.38-25.48,30.58-51.86-3.55-16.15-20.15-31.69-35.58-30.62-24.85,1.73-29.31,45.36-54.99,47.49-17.56,1.45-32.23-17.56-36.24-22.75-28.17-36.49-11.3-87.66-16.25-88.47Z"/>
    <path class="cls-15" d="M2728.23,474.51c29.63-34.79,53.73-41.97,70.62-41.75,22.66.3,34.11,13.96,70.4,27.14,27.47,9.98,51.7,13.34,68.19,14.6-17.04,9.32-54.3,26.74-104.61,26.74s-87.56-17.42-104.61-26.74Z"/>
    <path class="cls-15" d="M2937.44,403.27c49.75-36.37,59.03-96.06,67.03-93.66,4.4,1.32,2.37,19.66,0,40.99-4.52,40.73-6.78,61.1-18.63,71.3-23.27,20.04-68.84,10.27-70.8,0-.99-5.17,9.35-9.09,22.41-18.63Z"/>
    <path class="cls-15" d="M2722.6,331.16c-21.07,1.31-47.24,12.04-48.1,25.25-1.02,15.66,33.88,29.72,53.73,34.87,30.96,8.04,75.33,7.4,79.75-7.21,5.02-16.6-40.2-55.71-85.38-52.91Z"/>
    <path class="cls-15" d="M2842.85,131.54c-18.39,15.16-3.01,75.12,19.24,80.57,13.13,3.21,19.79-14.71,45.13-21.65,37.19-10.19,69.71,15.59,71.52,12.32,3.34-6.05-106.94-95.11-135.89-71.24Z"/>
  </g>
  <rect class="cls-13" x="-7.66" y="1951.38" width="3180.54" height="2888.68"/>
  <path class="cls-1" d="M3176.71,6747.69c-848.63-236.2-1697.26-472.4-2545.89-708.6-211.55,236.2-423.1,472.4-634.65,708.6v-1908.21c682.83-315.53,1365.66-631.05,2048.5-946.58,377.35,315.53,754.7,631.05,1132.05,946.58v1908.21Z"/>
  <path class="cls-6" d="M3185.53,7810.66H-3.83v-1071.49c210.53-237.94,421.05-475.88,631.58-713.82,852.59,237.94,1705.19,475.88,2557.78,713.82v1071.49Z"/>
  <polygon class="cls-14" points="1343.28 1940.48 657.67 2620.85 -7.66 2759.52 -7.66 1938.62 1343.28 1940.48"/>
  <path class="cls-14" d="M2241.38,1910.02c111.82,344.24,223.64,688.47,335.45,1032.71l276.15,776.18,163.52-761.57-187.21-662.11,343.59-343.85c-310.5-13.78-621-27.57-931.5-41.35Z"/>
  <polygon class="cls-14" points="794.65 3272.79 147.74 4013.21 456.42 5606.5 1004.15 4341.24 794.65 3272.79"/>
  <polygon class="cls-14" points="2241.38 4279.58 2741.42 4279.58 2741.42 4840.06 2146.11 5268.48 2241.38 4731.53 2241.38 4279.58"/>
  <polygon class="cls-14" points="1760.61 2105.94 1113.71 2846.35 1422.38 4439.65 1970.12 3174.38 1760.61 2105.94"/>
  <g>
    <path class="cls-3" d="M1203.15,5789.9c68.04-63.77,197.25-167.15,386.61-220.35,405.31-113.87,739.6,99.53,798.25,138.59,314.35,209.37,487.65,590.17,418.27,663.02-26.37,27.7-67.53-10.4-162.22,10.49-188.94,41.68-313.95,257.08-277.2,313.74,14.09,21.74,51.33,19.09,60.49,49.65,6.17,20.59-4.02,44.19-17.78,57.78-28.75,28.4-76.96,16.94-89.86,13.87-40.35-9.59-46.63-30.12-95.22-49.87-33.64-13.67-60.11-15.82-75.16-17.88-62.82-8.61-290.72-65.66-473.64-182.73-105.44-67.48-263.42-168.6-321.87-361.23-39.11-128.91-25.18-278.05-6.79-280.92,7.83-1.22,12.46,24.69,32.69,49.54,44.17,54.23,127.77,60.41,199.2,54.85,37,68.09,75.14,119.73,104.26,155.27,59.66,72.8,173.01,211.13,342.12,269.83,34.51,11.97,68.28,19.58,110.92,49.16,75.57,52.42,111.77,127.25,117.21,123.93,3.29-2.01-7-31.2-25.07-59.62-63.54-99.89-166-100.14-259.62-162.27-76.14-50.53-46.75-75.83-162.39-224.45-61.32-78.81-160.56-203.96-321.58-293.47-58.69-32.63-152.66-75.56-281.61-96.94Z"/>
    <path class="cls-6" d="M1792.18,5605.79c-1.65,12.48,14.55,23.95,21.18,28.64,30.23,21.39,71.57,17.82,99.02,2.81,2.86-1.56,22.65-12.38,20.8-20.52-1.44-6.32-14.66-5.46-35.55-11.64-33.46-9.9-34.85-22.54-56.13-23.89-21.65-1.37-47.39,10.01-49.33,24.61Z"/>
    <path class="cls-2" d="M2128.43,5930.84c15.42,48.27,89.05,63.95,137.11,60.06,11.39-.92,88.64-8.38,111.55-59.1,24.44-54.11-24.22-132.59-79.12-173.73-25.91-19.42-53.21-30.52-74.86-26.82-10.28,1.76-33.7,9.33-40.08,26.8-7.01,19.19,13.15,32.2,7.48,53.49-5.65,21.22-28.71,19.67-47.32,43.86-15.84,20.58-22.31,51.8-14.76,75.43Z"/>
    <path class="cls-6" d="M2343.48,5844.65l-45.5-86.57,45.5,86.57Z"/>
    <path class="cls-16" d="M2128.69,5885.25c9.72,13.46,31.55,39.49,68.79,53.89,12.93,5,58.3,22.55,101.17.68,8.15-4.16,29.73-15.55,40.89-40.34,9.28-20.63,6.43-39.6,3.93-54.83-6.94-42.31-31.12-71.81-45.51-86.57,11.83,7.89,29.07,21.25,44.88,41.9,10.37,13.55,59.23,77.41,34.24,131.82-22.12,48.16-87.02,56.1-111.55,59.1-14.25,1.74-57.75,6.46-98.37-18.24-12.82-7.79-30.92-19.16-38.74-41.83-6.8-19.72-2.29-37.67.27-45.59Z"/>
    <path class="cls-2" d="M2231.44,6224.89c2.53-5.51,9.81-22.39,12.81-53.83,4.05-42.5-5.13-55-2.61-73.63,6.61-48.97,88.65-101.02,166.52-92.19,81.17,9.21,131.61,81.61,148.36,139.94,19.07,66.38,4.69,150.03-46.92,192.53-18.75,15.43-59.26,24.84-140.27,43.64-26.02,6.04-46.08,9.69-71.59,6.78-23.47-2.69-57.34-6.56-79.6-34.75-20.14-25.5-23.92-62.23-12.88-88.22,7.22-17,15.41-16.74,26.19-40.26Z"/>
    <path class="cls-19" d="M2205.25,6265.15c6.05,20.01,19.78,52.93,51.35,72.93,42.98,27.23,93.3,14.2,148.27-.04,51.18-13.26,78.47-20.33,100.88-45.97,47.52-54.38,25.82-138.2,19.76-161.61-13.49-52.11-41.65-87.43-57.96-105.1,18.33,12.31,59.14,43.37,81.8,99.16,5.12,12.61,40.82,104.41-7.95,174.25-23.17,33.18-54.91,46.38-101.33,65.69-62.54,26.02-111.29,25.06-122.71,24.65-35.38-1.25-63.88-2.25-86.44-23.16-36.85-34.16-27.08-92.94-25.66-100.8Z"/>
    <path class="cls-24" d="M1203.15,5789.9c95.3-7.35,190.6-14.71,285.9-22.06h0c253.56,108.93,340.27,230.75,369.04,327.98,15.34,51.85,26.81,139.29,76.44,152.29,55.57,14.56,126.75-72.64,135.46-63.87,7.16,7.2-54.48,52.29-51.25,107.8,2.73,47.08,50.88,80.23,134.13,135.95,71.06,47.57,106.59,71.35,137.62,77.35,122.71,23.71,275.78-71.1,330.1-192.82,29.95-67.11,24.34-129.99,51.67-134.61,38.37-6.47,81.9,111.98,103.12,104.52,19.81-6.96-9.98-113.14,3.46-117.28,11.85-3.65,60.08,71.14,50.17,151.42-2.31,18.71-6.94,56.25-31.99,68.86-20,10.07-37.06-3.76-84.45-7.28,0,0-25.15-1.86-68.53,3.5-108.65,13.43-268.71,163.49-268.55,259.78.03,18.69.08,42.98,15.66,64.48,14.3,19.73,30.31,21.39,36.17,39.14,6.99,21.18-6.74,46.23-17.78,57.78-28.93,30.28-95.31,26.39-163.81-12.06-263.69-53.83-453.57-151.95-570.07-224.55-135.39-84.37-242.5-153.12-306.25-291.02-69.56-150.45-48.3-298.38-32.37-370.38,76.65,152.93,157.95,240.13,219.93,292.07,28.54,23.92,72.26,56.51,136.55,117.69,73.57,70.02,67.67,76.2,124.15,125.84,79.78,70.12,149,108.26,190.19,130.63,22.89,12.43,52.53,27.46,91.57,41.03,36.38,12.65,121.55,42.26,141.09,15.2,4.53-6.28,7.96-18.96-5.17-47.04-14.07-30.08-33.7-50.58-51.67-64.32-53.36-40.81-85.28-39.12-161.94-79.97,0,0-29.52-15.73-61.57-37.08-92.97-61.92-104.57-135.09-188.79-252.91-31.05-43.43-91.19-126.13-193.34-198.91-64.6-46.03-128.89-75.88-152.67-86.8-54.64-25.1-103.91-41.88-143.45-53.26l-78.78-17.08Z"/>
    <path class="cls-25" d="M1406.44,6340.92c-95.27-261.47-59.84-306.05-43.24-316.7,35.99-23.08,111.66,31.15,166.02,70.13,119.77,85.85,114.62,135.93,237.7,240.94,82.06,70.01,137.24,117.09,225.04,140.28,45.93,12.13,85.27,13.63,139.33,42.91,54.4,29.47,90.61,69.15,113.03,98.77-20.09-32.68-53.87-78.03-108.1-114.53-57.2-38.51-90.14-35.53-165.12-71.43-77.07-36.9-126.97-80.58-167.25-115.85-81.25-71.14-129.78-139.75-161.27-184.87-27.89-39.96-48.9-75.42-63.67-102.1-31.05,1.24-71.25-1.8-114.38-17.74-26.69-9.86-84-31.05-117.51-86.65-4.69-7.78-7.92-14.6-9.97-19.26-5.49,23.81-12.06,58.78-14.62,101.49-2.35,39.22-6.52,122.08,27.27,219.53,10.02,28.91,27.11,69.84,56.74,115.09Z"/>
  </g>
  <polygon class="cls-1" points="230.52 4026.35 751.98 3470.7 333.11 4124.98 230.52 4026.35"/>
  <polygon class="cls-1" points="1444.27 4124.98 1251.36 2787 1513.55 2541.57 1444.27 4124.98"/>
  <polygon class="cls-1" points="2512.44 2469.13 2819.79 3395.72 2868.14 3034.72 2446.21 2043.09 2512.44 2469.13"/>
  <polygon class="cls-1" points="2343.48 4342.65 2657.18 4774.03 2566.74 4439.65 2343.48 4342.65"/>
  <polygon class="cls-1" points="78.68 2588.24 615.95 2515.68 940.74 2147.46 78.68 2588.24"/>
  <path class="cls-3" d="M338.11,6953.44c88.83-130.99,227.1-334.88,232.92-582.99,2.18-92.87-15.41-140.62,20.39-172.84,82.05-73.85,342.79,25.45,355.41,136.85,6.04,53.27-46.19,95.71-97.17,142.71-79.44,73.26-479.03,455.65-439.93,763.91,16.5,130.09,104.03,191,76.4,219.68-44.86,46.58-316.51-71.63-333.92-224.83-9.47-83.3,64.57-103.6,185.89-282.49Z"/>
  <path class="cls-25" d="M782.12,6191.82c16.8,11.15,68.71,48.74,87.23,117.66,20.69,76.98-8.52,162.41-74.32,220.08,44.06-36.35,77.38-70.02,100.74-95.54,39.43-43.08,49.11-61.47,51.07-82.79,3.14-34.17-14.91-61.15-24.65-75.72-42.66-63.78-117.01-79.69-140.07-83.69Z"/>
  <path class="cls-25" d="M264.77,7051.47c5.48,5.5-66.89,51.6-73.48,132.07-4.01,48.88,16.37,108.98,59.09,146.71,53.81,47.52,106.9,27.63,181.35,91.69,11.51,9.91,42.91,36.92,37.25,47.44-8.49,15.8-98.33-9.95-169.61-51.67-38.2-22.36-87.29-51.92-121.4-111.74-15.28-26.8-39.87-69.94-31.52-122.43,14.14-88.9,112.2-138.2,118.31-132.07Z"/>
  <path class="cls-1" d="M1488.47,7277.86c.03-27.88-99.01-51.17-159.5-59.81-182.48-26.07-385.26,28.22-383.94,69.57,1.32,41.37,207.23,77.66,383.94,50.06,10.27-1.6,159.46-25.6,159.5-59.81Z"/>
  <path class="cls-1" d="M1807.46,6941.97c-45.14-59.58-133.24-59.76-159.5-59.81-18.9-.04-132.58,1.86-159.5,59.81-42.39,91.26,135.26,311.09,243.67,279.55,80.91-23.54,141.38-192.36,75.33-279.55Z"/>
  <path class="cls-1" d="M2465.17,7263.58c-9.09-16.03-57.54,16.4-339.89,90.89-105.43,27.81-159.02,39.21-159.5,59.81-.61,26.03,83.8,57.9,159.5,59.81,189.93,4.79,358.23-178.17,339.89-210.51Z"/>
  <path class="cls-1" d="M2878.38,7084.29c59.5-93.69-57.15-304.21-166.86-301.39-106.84,2.75-213.83,208-152.13,301.39,39.5,59.79,138.95,59.81,159.5,59.81,19.38,0,121.5.02,159.5-59.81Z"/>
  <path class="cls-1" d="M941.61,7578.44c-27.09-20.29-80.72-59.26-159.5-59.81-62.94-.44-156.06,23.55-159.5,59.81-7.29,76.84,388.26,208.19,424.55,147.79,18.29-30.44-54.62-109.64-105.56-147.79Z"/>
  <path class="cls-1" d="M896.04,7001.79c175.11-125.68,272.56-421.82,205.2-471.24-97.41-71.47-581.32,342.46-524.2,471.24,19.82,44.68,107.25,60.65,159.5,59.81,76.36-1.23,130.83-39.23,159.5-59.81Z"/>
  <path class="cls-1" d="M2894.48,7401.78c-28.46-5.77-41.04,108.45-153.06,176.67-84.65,51.55-153.76,32.79-159.5,59.81-9.65,45.44,169.28,176.01,279.81,118.02,129.06-67.71,75.8-345.77,32.75-354.5Z"/>
  <path class="cls-6" d="M607.81,7015.49c32.79-53.51,76.12-115.03,132.78-178.23,67.86-75.7,135.92-132.68,193.58-174.51-38.31,10.01-199.42,56.68-282.21,210.75-28.25,52.57-39.6,103.18-44.16,142Z"/>
  <path class="cls-6" d="M1549.87,6924.88c-.49-8.54,105.77-55.61,193.18,0,63.13,40.17,77.14,108.9,79.54,121.95-29-34.89-73.17-76.78-136.42-100.44-75.11-28.1-135.93-15.08-136.3-21.51Z"/>
  <path class="cls-6" d="M978.21,7278.35c27.7-11.99,58.8-23.03,93.21-31.66,144.01-36.09,270.75-11,347.26,12.19-146.82,6.49-293.64,12.98-440.47,19.47Z"/>
  <path class="cls-6" d="M679.3,7570.07c15.09-11.71,52.62-37.34,104.64-38.23,102.22-1.76,160.65,93.68,163.37,98.3-27.49-18.05-74.2-43.77-137.39-56.4-54.13-10.81-100.13-7.98-130.62-3.67Z"/>
  <path class="cls-6" d="M2102.75,7428.7c26.91-5.8,67-16.07,113.41-34.73,101.83-40.95,156.77-91.19,164.44-82.49,6.74,7.65-25.42,58.03-73.8,90.08-81.72,54.14-176.51,34.02-204.04,27.13Z"/>
  <path class="cls-6" d="M2567.39,6963.49c-22.79-33.72,78.65-178.55,150.47-159.69,31.31,8.22,57.09,47.58,52.88,79.84-9.76,74.74-180.7,113.37-203.36,79.84Z"/>
  <path class="cls-6" d="M2705.74,7717.24c-.34,5.52,79.16,37.53,140.02,0,63.08-38.9,68.2-129.12,63.34-131.02-4.4-1.72-15.36,69.6-75.01,107.69-59.28,37.86-128.03,18.22-128.35,23.34Z"/>
</svg>
@endif
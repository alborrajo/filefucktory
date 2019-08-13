class API {
	
	constructor(token=null) {
		this.token = token;
		
		this.restRoute = CONFIG.restRoute;
	}	
	
	getInvite(inviteID) {
		return new Promise((resolve, reject) => {
			const xhr = new XMLHttpRequest();
			xhr.open("GET", this.restRoute+"user/invites/"+inviteID);
			xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if(xhr.status === 200) {resolve(JSON.parse(xhr.responseText));}
					else {reject(xhr.responseText);}
				}
			}
			xhr.send();
		});
	}
	
	register(inviteID, username, password) {
		return new Promise((resolve, reject) => {
			const xhr = new XMLHttpRequest();
			xhr.open("POST", this.restRoute+"user/invites/"+inviteID);
			xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhr.setRequestHeader("Content-Type", "application/json");
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if(xhr.status === 201) {resolve(JSON.parse(xhr.responseText));}
					else {reject(xhr.responseText);}
				}
			}
			xhr.send(JSON.stringify({
				"username": username,
				"password": password
			}));
		});
	}
	
	
	// Functions that REQUIRE token	
	getUser() {
		return new Promise((resolve, reject) => {
			const xhr = new XMLHttpRequest();
			xhr.open("GET", this.restRoute+"user");
			xhr.setRequestHeader('Authorization', 'Basic '+this.token);
			xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if(xhr.status === 200) {resolve(JSON.parse(xhr.responseText));}
					else {reject(xhr.responseText);}
				}
			}
			xhr.send();
		});
	}
	
	getInvites() {
		return new Promise((resolve, reject) => {
			const xhr = new XMLHttpRequest();
			xhr.open("GET", this.restRoute+"user/invites");
			xhr.setRequestHeader('Authorization', 'Basic '+this.token);
			xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if(xhr.status === 200) {resolve(JSON.parse(xhr.responseText));}
					else {reject(xhr.responseText);}
				}
			}
			xhr.send();
		});
	}
	
	addInvite() {
		return new Promise((resolve, reject) => {
			const xhr = new XMLHttpRequest();
			xhr.open("POST", this.restRoute+"user/invites");
			xhr.setRequestHeader('Authorization', 'Basic '+this.token);
			xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if(xhr.status === 201) {resolve();}
					else {reject(xhr.responseText);}
				}
			}
			xhr.send();
		});
	}
	
	getFolder(folder) {
		return new Promise((resolve, reject) => {
			const xhr = new XMLHttpRequest();
			xhr.open("GET", this.restRoute+"files/"+folder+"/");
			xhr.setRequestHeader('Authorization', 'Basic '+this.token);
			xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if(xhr.status === 200) {resolve(JSON.parse(xhr.responseText));}
					else {reject(xhr.responseText);}
				}
			}
			xhr.send();
		});
	}
	
	mkdir(folder, folderName, setPublic) {
		return new Promise((resolve, reject) => {
			const xhr = new XMLHttpRequest();
			xhr.open("PUT", this.restRoute+"files/"+folder+"/");
			xhr.setRequestHeader('Authorization', 'Basic '+this.token);
			xhr.setRequestHeader("Content-Type", "application/json");
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if(xhr.status === 204) {resolve(xhr.responseText);}
					else {reject(xhr.responseText);}
				}
			}
			xhr.send(JSON.stringify({
				"public": setPublic,
				"name": folderName
			}));
		});
	}
	
	rm(path) {
		return new Promise((resolve, reject) => {
			const xhr = new XMLHttpRequest();
			xhr.open("DELETE", this.restRoute+"files/"+path);
			xhr.setRequestHeader('Authorization', 'Basic '+this.token);
			xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if(xhr.status === 204) {resolve(xhr.responseText);}
					else {reject(xhr.responseText);}
				}
			}
			xhr.send();
		});
	}
	
	setPublic(path, setPublicValue) {
		return new Promise((resolve, reject) => {
			const xhr = new XMLHttpRequest();
			xhr.open("PUT", this.restRoute+"files/"+path);
			xhr.setRequestHeader('Authorization', 'Basic '+this.token);
			xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhr.setRequestHeader("Content-Type", "application/json");
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if(xhr.status === 204) {resolve(xhr.responseText);}
					else {reject(xhr.responseText);}
				}
			}
			xhr.send(JSON.stringify({
				"public": setPublicValue
			}));
		});
	}
	
	move(from, to) {
		return new Promise((resolve, reject) => {
			const xhr = new XMLHttpRequest();
			xhr.open("PUT", this.restRoute+"files/"+from);
			xhr.setRequestHeader('Authorization', 'Basic '+this.token);
			xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhr.setRequestHeader("Content-Type", "application/json");
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if(xhr.status === 204) {resolve(xhr.responseText);}
					else {reject(xhr.responseText);}
				}
			}
			xhr.send(JSON.stringify({
				"moveTo": to
			}));
		});
	}
	
	upload(path, file, onprogress=undefined) {
		return new Promise((resolve, reject) => {
			const xhr = new XMLHttpRequest();
			xhr.open("POST", this.restRoute+"files/"+path);
			xhr.setRequestHeader('Authorization', 'Basic '+this.token);
			xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhr.setRequestHeader("Content-Type", "application/octet-stream");
			
			xhr.upload.onprogress = onprogress;
			
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if(xhr.status === 201) {resolve();}
					else {reject(xhr.responseText);}
				}
			}
			
			xhr.send(file);
		});
	}
	
	getDownloadToken(path) {
		return new Promise((resolve, reject) => {
			// Create URL with query parameters
			let target = this.restRoute+"files/"+path;
			target += '?';
			target += new URLSearchParams({
				getToken: null
			}).toString();
			
			const xhr = new XMLHttpRequest();
			xhr.open("GET", target);
			xhr.setRequestHeader('Authorization', 'Basic '+this.token);
			xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhr.setRequestHeader("Content-Type", "application/json");
						
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if(xhr.status === 200) {resolve(JSON.parse(xhr.responseText));}
					else {reject(xhr.responseText);}
				}
			}
			
			xhr.send(file);
		});
	}

}

module.exports = API;
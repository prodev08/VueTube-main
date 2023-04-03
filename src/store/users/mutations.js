export default {
	login(state, payload) {
		state.loggedIn = true;

		state.googleUser = payload;

		if(state.googleUser.email == state.adminEmail){
			state.admin = true;
		}

		localStorage.setItem("googleUser", JSON.stringify(state.googleUser) );
		//sessionStorage.setItem("googleUser", "Smith");
	},
	logout(state){
		state.loggedIn = false;
		state.admin = false;

		state.googleUser = null

		localStorage.removeItem("googleUser");
		//sessionStorage.removeItem("googleUser");
	},
}
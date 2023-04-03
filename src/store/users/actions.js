export default {
	async login(context, payload) {

		// Build our google user out of our firebase result payload
		const googleUser = {
			email: payload.user.email,
			photoURL: payload.user.photoURL,
			uid: payload.user.uid,
			idToken: payload.credential.idToken,
			accessToken: payload.credential.accessToken,
			refreshToken: payload.user.refreshToken,
			tokenExpiration: null
		};

		// Log a user in and / or create their account
		const response = await fetch(process.env.VUE_APP_URL+'user/login.php', {
			//mode: 'no-cors',
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ googleUser: googleUser })
		}).then(response => {
			if(response.ok){
				return response.json();
			}
		})
		.then(data => {
			if(data.success){
				context.commit('login', googleUser);
				return googleUser;
			}else if(data.error){
				console.error('Server could not log in!', data.error);
				return false
			}
		})
		.catch(error => {
			console.error('Could not login!', error);
		});

		const responseData = await response;

		if(responseData === false){
			const error = new Error(
				responseData.message || 'Unable to login'
			);

			throw error;
		}

		// On success this should be the google user
		return googleUser; // responseData
	},
	autoLogin(context){

		const googleUser =  JSON.parse(localStorage.getItem('googleUser'));

		if(googleUser && googleUser.accessToken){
			context.commit('login', googleUser);
		}
	},
	logout(context) {

		// This basically just tracks the last visit / login. However it will also create a user if one does not exist
		fetch(process.env.VUE_APP_URL+'user/logout.php', {
			//mode: 'no-cors',
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ googleUser: context.getters.getGoogleUser })
		}).then(response => {
			if(response.ok){
				return response.json();
			}
		})
		.then(data => {
			if(data.success){
				context.commit('logout');
			}else if(data.error){
				console.error('Server could not log out!', data.error);
			}
		})
		.catch(error => {
			console.error('Could not log out!', error);
		});
	},
	addToHistory({ getters }, payload){
		
		let googleUser = getters.getGoogleUser;

		if(googleUser && googleUser.accessToken){
			fetch(process.env.VUE_APP_URL+'videos/watched.php', {
				//mode: 'no-cors',
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ googleUser: googleUser, video_id: payload.video.video_id, channel_id: payload.video.channel_id })
			});
		}

	},
	unHistory({ getters }, payload){
		
		let googleUser = getters.getGoogleUser;

		if(googleUser && googleUser.accessToken){
			fetch(process.env.VUE_APP_URL+'videos/unhistory.php', {
				//mode: 'no-cors',
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ googleUser: googleUser, video_id: payload.video.video_id })
			})
		}

	},
	async toggleLiked({ getters }, payload){
		
		let googleUser = getters.getGoogleUser;

		if(googleUser && googleUser.accessToken){
			const response = await fetch(process.env.VUE_APP_URL+'videos/liked.php', {
				//mode: 'no-cors',
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ googleUser: googleUser, video_id: payload.video.video_id })
			})

			const responseData = await response;

			return responseData;
		}

	},
	async toggleWatchLater({ getters }, payload){
		
		let googleUser = getters.getGoogleUser;

		if(googleUser && googleUser.accessToken){
			const response = await fetch(process.env.VUE_APP_URL+'videos/watch-later.php', {
				//mode: 'no-cors',
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ googleUser: googleUser, video_id: payload.video.video_id })
			})

			const responseData = await response;

			return responseData;
		}

	},
}
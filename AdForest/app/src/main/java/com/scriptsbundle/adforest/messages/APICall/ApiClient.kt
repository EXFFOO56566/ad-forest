package com.scriptsbundle.adforest.messages.APICall

import com.androidnetworking.interceptors.HttpLoggingInterceptor
import com.google.gson.GsonBuilder
import okhttp3.Interceptor
import okhttp3.OkHttpClient
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import com.scriptsbundle.adforest.BuildConfig.DEBUG
import com.scriptsbundle.adforest.utills.Network.RestService
import com.scriptsbundle.adforest.utills.UrlController
import java.util.concurrent.TimeUnit

object ApiClient {

    val instance: RestService = Retrofit.Builder().run {
        val gson = GsonBuilder()
                .enableComplexMapKeySerialization()
                .setPrettyPrinting()
                .create()

        baseUrl(UrlController.Base_URL)
        addConverterFactory(GsonConverterFactory.create(gson))
        client(createRequestInterceptorClient())
        build()
    }.create(RestService::class.java)


    private fun createRequestInterceptorClient(): OkHttpClient {
        val interceptor = Interceptor { chain ->
            val original = chain.request()
            val requestBuilder = original.newBuilder()
            val request = requestBuilder.build()
            chain.proceed(request)
        }

        return if (DEBUG) {
            OkHttpClient.Builder()
                    .addInterceptor(interceptor)
                    .addInterceptor(HttpLoggingInterceptor().setLevel(HttpLoggingInterceptor.Level.BODY))
                    .connectTimeout(29000, TimeUnit.SECONDS)
                    .readTimeout(29000, TimeUnit.SECONDS)
                    .writeTimeout(29000, TimeUnit.SECONDS)
                    .build()
        } else {
            OkHttpClient.Builder()
                    .addInterceptor(interceptor)
                    .connectTimeout(29000, TimeUnit.SECONDS)
                    .readTimeout(29000, TimeUnit.SECONDS)
                    .writeTimeout(29000, TimeUnit.SECONDS)
                    .build()
        }
    }
}
package com.scriptsbundle.adforest

import androidx.appcompat.app.AppCompatActivity
import androidx.fragment.app.FragmentActivity
import androidx.lifecycle.lifecycleScope
import id.zelory.compressor.Compressor
import id.zelory.compressor.constraint.Constraint
import id.zelory.compressor.constraint.default
import kotlinx.coroutines.launch
import java.io.File

fun FragmentActivity.compress(
        inputFile: File,
        callback: Callback,
        vararg constraints: Constraint
) {
    val context = this
    var outputFile: File? = null
    lifecycleScope.launch {
        try {
            outputFile = Compressor.compress(context, inputFile) {
                if (constraints.isEmpty()) {
                    default()
                } else {
                    for (con in constraints)
                        constraint(con)
                }
            }.also {
                callback.onComplete(true, it)
            }
        } catch (e: Exception) {
            callback.onComplete(false, outputFile)
        }
    }
}

class JavaCompressor {
    companion object {
        @JvmStatic
        fun compress(activity: FragmentActivity, inputFile: File,
                     callback: Callback,
                     vararg constraints: Constraint) {
            activity.compress(inputFile, callback, *constraints)
        }
    }
}

interface Callback {
    fun onComplete(status: Boolean, file: File?)
}
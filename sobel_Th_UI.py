import numpy as np
import cv2
import matplotlib.pyplot as plt

def nothing(x):
	pass

def Threshold_image(edge_image,Th):
	ret, img_thresh = cv2.threshold(edge_image, Th, 255, cv2.THRESH_BINARY)
	return img_thresh

# Sobelフィルタを用いて特徴量検出　入力np.array型の画素値
def Sobel_RGB(Image):
	sobel_edge_ = np.empty((Image.shape[0], Image.shape[1], 3), dtype=np.uint8)
	for i in range(0,3):
		# x.y方向の特徴量を得る
		sobel_image_x = cv2.Sobel(Image[:,:,i],cv2.CV_32F,1,0)
		sobel_image_y = cv2.Sobel(Image[:,:,i],cv2.CV_32F,0,1)
		# それぞれを8ビット変換
		abs_sobel_x = cv2.convertScaleAbs(sobel_image_x)
		abs_sobel_y = cv2.convertScaleAbs(sobel_image_y)
		# X、Yの重みを半々にして一つの画素値を取得
		sobel_edge_temp = cv2.addWeighted(abs_sobel_x, 0.5, abs_sobel_y, 0.5, 0)
		sobel_edge_[:,:,i] = sobel_edge_temp
	
	print(sobel_edge_.shape)
	# sobel_edge_の中に格納されているRGBのSobel値から最大のものを出力
	sobel_edge = np.max(sobel_edge_, axis=2)
	return sobel_edge

# メイン関数
if __name__ == '__main__':
	img = cv2.imread("image_file")
	
	#前処理
	img = cv2.medianBlur(img, 5)

	cv2.namedWindow("Sobel")
	cv2.createTrackbar("Threshold", "Sobel", 0, 1000, nothing)
	
	edge_image = Sobel_RGB(img)
	edge_img_Th = edge_image
	
	# ここからインタラクティブな更新
	while(1):
		cv2.imshow("edge",edge_img_Th)
		k=cv2.waitKey(1) &0xFF
		if k==27:
			break;
		#getTrackbarPosはTrackBarによって動かされた値をThに返す関数
		Th = cv2.getTrackbarPos("Threshold", "Sobel")

		edge_img_Th = Threshold_image(edge_image, Th)

cv2.destroyAllWindows()

type Props = {
	className?: string;
};

const Logo = ({ className = "" }: Props) => (
	<span className={className} style={{ fontFamily: "icomoon" }}>
		{"\ue900"} {/* Replace this with the correct Unicode character */}
	</span>
);

export default Logo;
